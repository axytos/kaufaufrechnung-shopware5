<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Tests\Core;

use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutOrderStatus;
use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutPaymentStatus;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\Core\OrderRepository;
use AxytosKaufAufRechnungShopware5\Core\OrderStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

class OrderStateMachineTest extends TestCase
{
    /** @var OrderRepository&MockObject */
    private OrderRepository $orderRepository;

    /** @var PluginConfiguration&MockObject */
    private PluginConfiguration $pluginConfiguration;

    private OrderStateMachine $sut;

    public function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->pluginConfiguration = $this->createMock(PluginConfiguration::class);

        $this->sut = new OrderStateMachine($this->orderRepository, $this->pluginConfiguration);
    }

    public function test_setCanceled_calls_saveOrderStatus(): void
    {
        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('saveOrderStatus')
            ->with($order, Status::ORDER_STATE_CANCELLED);

        $this->sut->setCanceled($order);
    }

    public function test_setPaymentReview_calls_savePaymentStatus(): void
    {
        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('savePaymentStatus')
            ->with($order, Status::PAYMENT_STATE_REVIEW_NECESSARY);

        $this->sut->setPaymentReview($order);
    }

    public function test_setPendingPayment_calls_savePaymentStatus(): void
    {
        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('savePaymentStatus')
            ->with($order, Status::PAYMENT_STATE_OPEN);

        $this->sut->setPendingPayment($order);
    }

    public function test_setTechnicalError_calls_savePaymentStatus(): void
    {
        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('savePaymentStatus')
            ->with($order, Status::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED);

        $this->sut->setTechnicalError($order);
    }

    public function test_setComplete_calls_saveOrderStatus(): void
    {
        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        $this->orderRepository
            ->expects($this->once())
            ->method('saveOrderStatus')
            ->with($order, Status::ORDER_STATE_COMPLETED);

        $this->sut->setComplete($order);
    }

    public function test_setConfiguredAfterCheckoutOrderStatus_calls_saveAfterCheckoutOrderStatus(): void
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

    public function test_setConfiguredAfterCheckoutOrderStatus_calls_saveAfterCheckoutPaymentStatus(): void
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
