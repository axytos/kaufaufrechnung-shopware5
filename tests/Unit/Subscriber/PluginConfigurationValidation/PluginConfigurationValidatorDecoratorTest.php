<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Subscriber\PluginConfigurationValidation;

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\DispatchRepository;
use AxytosKaufAufRechnungShopware5\Subscriber\PluginConfigurationValidation\PluginConfigurationValidatorDecorator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Dispatch\Dispatch;
use Shopware\Models\Payment\Payment;

class PluginConfigurationValidatorDecoratorTest extends TestCase
{
    /** @var PluginConfigurationValidator&MockObject */
    private $pluginConfigurationValidator;

    /** @var DispatchRepository&MockObject */
    private $dispatchRepository;

    /** @var PluginConfigurationValidatorDecorator */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->pluginConfigurationValidator = $this->createMock(PluginConfigurationValidator::class);
        $this->dispatchRepository = $this->createMock(DispatchRepository::class);
        $this->sut = new PluginConfigurationValidatorDecorator(
            $this->pluginConfigurationValidator,
            $this->dispatchRepository
        );
    }

    /**
     * @return void
     */
    public function test_isInvalid_calls_decorator_isInvalid()
    {
        $this->pluginConfigurationValidator
            ->method("isInvalid")
            ->willReturn(true);

        $this->pluginConfigurationValidator
            ->expects($this->once())
            ->method("isInvalid");

        $this->dispatchRepository
            ->expects($this->never())
            ->method("findAll");

        $actual = $this->sut->isInvalid();

        $this->assertTrue($actual);
    }

    /**
     * @return void
     */
    public function test_isInvalid_checks_database_and_returns_true_if_no_dispatches_exist()
    {
        $this->pluginConfigurationValidator
            ->method("isInvalid")
            ->willReturn(false);

        $this->dispatchRepository
            ->method("findAll")
            ->willReturn([]);

        $this->pluginConfigurationValidator
            ->expects($this->once())
            ->method("isInvalid");

        $this->dispatchRepository
            ->expects($this->once())
            ->method("findAll");

        $actual = $this->sut->isInvalid();

        $this->assertTrue($actual);
    }

    /**
     * @return void
     */
    public function test_isInvalid_checks_database_and_returns_true_if_no_dispatch_references_the_payment()
    {
        $this->pluginConfigurationValidator
            ->method("isInvalid")
            ->willReturn(false);

        /** @var Payment&MockObject */
        $payment1 = $this->createMock(Payment::class);
        $payment1
            ->method("getName")
            ->willReturn("payment_method_1");

        /** @var Payment&MockObject */
        $payment2 = $this->createMock(Payment::class);
        $payment2
            ->method("getName")
            ->willReturn("payment_method_2");

        /** @var Payment&MockObject */
        $payment3 = $this->createMock(Payment::class);
        $payment3
            ->method("getName")
            ->willReturn("payment_method_3");

        /** @var Dispatch&MockObject */
        $dispatch1 = $this->createMock(Dispatch::class);
        $dispatch1
            ->method("getPayments")
            ->willReturn([]);

        /** @var Dispatch&MockObject */
        $dispatch2 = $this->createMock(Dispatch::class);
        $dispatch2
            ->method("getPayments")
            ->willReturn([$payment1, $payment2]);

        /** @var Dispatch&MockObject */
        $dispatch3 = $this->createMock(Dispatch::class);
        $dispatch3
            ->method("getPayments")
            ->willReturn([$payment1, $payment3]);

        $this->dispatchRepository
            ->method("findAll")
            ->willReturn([$dispatch1, $dispatch2, $dispatch3]);

        $actual = $this->sut->isInvalid();

        $this->assertTrue($actual);
    }

    /**
     * @return void
     */
    public function test_isInvalid_checks_database_and_returns_false_if_one_dispatch_references_the_payment()
    {
        $this->pluginConfigurationValidator
            ->method("isInvalid")
            ->willReturn(false);

        /** @var Payment&MockObject */
        $payment1 = $this->createMock(Payment::class);
        $payment1
            ->method("getName")
            ->willReturn("axytos_kauf_auf_rechnung");

        /** @var Payment&MockObject */
        $payment2 = $this->createMock(Payment::class);
        $payment2
            ->method("getName")
            ->willReturn("payment_method_2");

        /** @var Payment&MockObject */
        $payment3 = $this->createMock(Payment::class);
        $payment3
            ->method("getName")
            ->willReturn("payment_method_3");

        /** @var Dispatch&MockObject */
        $dispatch1 = $this->createMock(Dispatch::class);
        $dispatch1
            ->method("getPayments")
            ->willReturn([]);

        /** @var Dispatch&MockObject */
        $dispatch2 = $this->createMock(Dispatch::class);
        $dispatch2
            ->method("getPayments")
            ->willReturn([$payment1, $payment2]);

        /** @var Dispatch&MockObject */
        $dispatch3 = $this->createMock(Dispatch::class);
        $dispatch3
            ->method("getPayments")
            ->willReturn([$payment2, $payment3]);

        $this->dispatchRepository
            ->method("findAll")
            ->willReturn([$dispatch1, $dispatch2, $dispatch3]);

        $actual = $this->sut->isInvalid();

        $this->assertFalse($actual);
    }
}
