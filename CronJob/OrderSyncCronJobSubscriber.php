<?php

namespace AxytosKaufAufRechnungShopware5\CronJob;

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use Axytos\KaufAufRechnung\Core\OrderSyncWorker;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use Enlight\Event\SubscriberInterface;

class OrderSyncCronJobSubscriber implements SubscriberInterface
{
    const RESULT_CODE_INVALD_CONFIG = 'INVALID_CONFIG';
    const RESULT_CODE_SUCCESS = 'SUCCESS';
    const RESULT_CODE_FAILED = 'FAILED';

    /**
     * @var PluginConfigurationValidator
     */
    private $pluginConfigurationValidator;

    /**
     * @var ErrorHandler
     */
    private $errorHandler;

    /**
     * @var OrderSyncWorker
     */
    private $orderSyncWorker;

    /**
     * @var LoggerAdapterInterface
     */
    private $logger;

    public function __construct(
        PluginConfigurationValidator $pluginConfigurationValidator,
        ErrorHandler $errorHandler,
        OrderSyncWorker $orderSyncWorker,
        LoggerAdapterInterface $logger
    ) {
        $this->pluginConfigurationValidator = $pluginConfigurationValidator;
        $this->errorHandler = $errorHandler;
        $this->orderSyncWorker = $orderSyncWorker;
        $this->logger = $logger;
    }

    /**
     * @return array<string, string|array{0:string, 1?: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents()
    {
        return [
            'Shopware_CronJob_Axytos_KaufAufRechnung_OrderSync' => 'execute',
        ];
    }

    /**
     * @param \Shopware_Components_Cron_CronJob $job
     *
     * @return string
     */
    public function execute($job)
    {
        try {
            $this->logger->info('CronJob Order Sync started');

            if ($this->pluginConfigurationValidator->isInvalid()) {
                $this->logger->info('CronJob Order Sync aborted: invalid config');

                return self::RESULT_CODE_INVALD_CONFIG;
            }

            $this->orderSyncWorker->sync();
            $this->logger->info('CronJob Order Sync succeeded');

            return self::RESULT_CODE_SUCCESS;
        } catch (\Throwable $th) {
            $this->logger->error('CronJob Order Sync failed');
            $this->errorHandler->handle($th);

            return self::RESULT_CODE_FAILED;
        } catch (\Exception $th) { // @phpstan-ignore-line because of php5 compatibility
            $this->logger->error('CronJob Order Sync failed');
            $this->errorHandler->handle($th);

            return self::RESULT_CODE_FAILED;
        }
    }
}
