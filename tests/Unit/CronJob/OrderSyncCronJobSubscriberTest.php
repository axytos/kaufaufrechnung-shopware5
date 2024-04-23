<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\CronJob;

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use Axytos\KaufAufRechnung\Core\OrderSyncWorker;
use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncCronJobSubscriber;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware_Components_Cron_CronJob;

class OrderSyncCronJobSubscriberTest extends TestCase
{
    /**
     * @var \Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator&MockObject
     */
    private $pluginConfigurationValidator;

    /**
     * @var \AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler&MockObject
     */
    private $errorHandler;

    /**
     * @var \Axytos\KaufAufRechnung\Core\OrderSyncWorker&MockObject
     */
    private $orderSyncWorker;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\OrderSyncCronJobSubscriber
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    #[Before]
    public function beforeEach()
    {
        $this->pluginConfigurationValidator = $this->createMock(PluginConfigurationValidator::class);
        $this->errorHandler = $this->createMock(ErrorHandler::class);
        $this->orderSyncWorker = $this->createMock(OrderSyncWorker::class);

        $this->sut = new OrderSyncCronJobSubscriber(
            $this->pluginConfigurationValidator,
            $this->errorHandler,
            $this->orderSyncWorker,
            $this->createMock(LoggerAdapterInterface::class)
        );
    }

    /**
     * @return void
     */
    public function test_getSubscribedEvents_subscribes_execute_method_to_event()
    {
        $subscribedEvents = OrderSyncCronJobSubscriber::getSubscribedEvents();

        $this->assertEquals('execute', $subscribedEvents['Shopware_CronJob_Axytos_KaufAufRechnung_OrderSync']);
    }

    /**
     * @return void
     */
    public function test_execute_returns_INVALID_CONFIG_when_config_is_not_valid()
    {
        $this->pluginConfigurationValidator->method('isInvalid')->willReturn(true);

        /** @var Shopware_Components_Cron_CronJob&MockObject */
        $job = $this->createMock(Shopware_Components_Cron_CronJob::class);

        $result = $this->sut->execute($job);

        $this->assertEquals('INVALID_CONFIG', $result);
    }

    /**
     * @return void
     */
    public function test_execute_runs_sync_when_config_is_valid()
    {
        $this->pluginConfigurationValidator->method('isInvalid')->willReturn(false);

        $this->orderSyncWorker->expects($this->once())->method('sync');

        /** @var Shopware_Components_Cron_CronJob&MockObject */
        $job = $this->createMock(Shopware_Components_Cron_CronJob::class);

        $this->sut->execute($job);
    }

    /**
     * @return void
     */
    public function test_execute_returns_SUCCESS_when_sync_succeeds()
    {
        $this->pluginConfigurationValidator->method('isInvalid')->willReturn(false);

        /** @var Shopware_Components_Cron_CronJob&MockObject */
        $job = $this->createMock(Shopware_Components_Cron_CronJob::class);

        $result = $this->sut->execute($job);

        $this->assertEquals('SUCCESS', $result);
    }

    /**
     * @return void
     */
    public function test_execute_returns_FAILED_when_sync_fails()
    {
        $this->pluginConfigurationValidator->method('isInvalid')->willReturn(false);

        $this->orderSyncWorker->method('sync')->willThrowException(new \Exception());

        /** @var Shopware_Components_Cron_CronJob&MockObject */
        $job = $this->createMock(Shopware_Components_Cron_CronJob::class);

        $result = $this->sut->execute($job);

        $this->assertEquals('FAILED', $result);
    }

    /**
     * @return void
     */
    public function test_execute_reports_error_when_sync_fails()
    {
        $this->pluginConfigurationValidator->method('isInvalid')->willReturn(false);

        $exception = new \Exception();
        $this->orderSyncWorker->method('sync')->willThrowException($exception);

        $this->errorHandler->expects($this->once())->method('handle')->with($exception);

        /** @var Shopware_Components_Cron_CronJob&MockObject */
        $job = $this->createMock(Shopware_Components_Cron_CronJob::class);

        $result = $this->sut->execute($job);
    }
}
