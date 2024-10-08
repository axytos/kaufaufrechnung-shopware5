<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Core;

use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutOrderStatus;
use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutPaymentStatus;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\Core\OrderStateMachine;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order;

/**
 * @internal
 */
class OrderStateMachineTest extends TestCase
{
    /** @var OrderRepository&MockObject */
    private $orderRepository;

    /** @var PluginConfiguration&MockObject */
    private $pluginConfiguration;

    /**
     * @var OrderStateMachine
     */
    private $sut;

    /**
     * @return void
     *
     * @before
     */
    #[Before]
    public function beforeEach()
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->pluginConfiguration = $this->createMock(PluginConfiguration::class);

        $this->sut = new OrderStateMachine($this->orderRepository, $this->pluginConfiguration);
    }

    /**
     * @return void
     */
    public function test_set_configured_after_checkout_order_status_calls_save_after_checkout_order_status()
    {
        /** @var AfterCheckoutOrderStatus&MockObject */
        $afterCheckoutOrderStatus = $this->createMock(AfterCheckoutOrderStatus::class);
        $this->pluginConfiguration->method('getAfterCheckoutOrderStatus')->willReturn($afterCheckoutOrderStatus);

        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('saveAfterCheckoutOrderStatus')
            ->with($order, $afterCheckoutOrderStatus)
        ;

        $this->sut->setConfiguredAfterCheckoutOrderStatus($order);
    }

    /**
     * @return void
     */
    public function test_set_configured_after_checkout_order_status_calls_save_after_checkout_payment_status()
    {
        /** @var AfterCheckoutPaymentStatus&MockObject */
        $afterCheckoutPaymentStatus = $this->createMock(AfterCheckoutPaymentStatus::class);
        $this->pluginConfiguration->method('getAfterCheckoutPaymentStatus')->willReturn($afterCheckoutPaymentStatus);

        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('saveAfterCheckoutPaymentStatus')
            ->with($order, $afterCheckoutPaymentStatus)
        ;

        $this->sut->setConfiguredAfterCheckoutPaymentStatus($order);
    }
}
