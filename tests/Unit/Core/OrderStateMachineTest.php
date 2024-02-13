<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Core;

use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutOrderStatus;
use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutPaymentStatus;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;
use AxytosKaufAufRechnungShopware5\Core\OrderStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

class OrderStateMachineTest extends TestCase
{
    /** @var OrderRepository&MockObject */
    private $orderRepository;

    /** @var PluginConfiguration&MockObject */
    private $pluginConfiguration;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Core\OrderStateMachine
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->pluginConfiguration = $this->createMock(PluginConfiguration::class);

        $this->sut = new OrderStateMachine($this->orderRepository, $this->pluginConfiguration);
    }

    /**
     * @return void
     */
    public function test_setConfiguredAfterCheckoutOrderStatus_calls_saveAfterCheckoutOrderStatus()
    {
        /** @var AfterCheckoutOrderStatus&MockObject */
        $afterCheckoutOrderStatus = $this->createMock(AfterCheckoutOrderStatus::class);
        $this->pluginConfiguration->method('getAfterCheckoutOrderStatus')->willReturn($afterCheckoutOrderStatus);

        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('saveAfterCheckoutOrderStatus')
            ->with($order, $afterCheckoutOrderStatus);

        $this->sut->setConfiguredAfterCheckoutOrderStatus($order);
    }

    /**
     * @return void
     */
    public function test_setConfiguredAfterCheckoutOrderStatus_calls_saveAfterCheckoutPaymentStatus()
    {
        /** @var AfterCheckoutPaymentStatus&MockObject */
        $afterCheckoutPaymentStatus = $this->createMock(AfterCheckoutPaymentStatus::class);
        $this->pluginConfiguration->method('getAfterCheckoutPaymentStatus')->willReturn($afterCheckoutPaymentStatus);

        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('saveAfterCheckoutPaymentStatus')
            ->with($order, $afterCheckoutPaymentStatus);

        $this->sut->setConfiguredAfterCheckoutPaymentStatus($order);
    }
}
