<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Core;

use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

class OrderStateMachine
{
    private OrderRepository $orderRepository;
    private PluginConfiguration $pluginConfiguration;

    public function __construct(
        OrderRepository $orderRepository,
        PluginConfiguration $pluginConfiguration
    ) {
        $this->orderRepository = $orderRepository;
        $this->pluginConfiguration = $pluginConfiguration;
    }

    public function setCanceled(Order $order): void
    {
        $this->orderRepository->saveOrderStatus($order, Status::ORDER_STATE_CANCELLED);
    }

    public function setPaymentReview(Order $order): void
    {
        $this->orderRepository->savePaymentStatus($order, Status::PAYMENT_STATE_REVIEW_NECESSARY);
    }

    public function setPendingPayment(Order $order): void
    {
        $this->orderRepository->savePaymentStatus($order, Status::PAYMENT_STATE_OPEN);
    }

    public function setTechnicalError(Order $order): void
    {
        $this->orderRepository->savePaymentStatus($order, Status::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED);
    }

    public function setComplete(Order $order): void
    {
        $this->orderRepository->saveOrderStatus($order, Status::ORDER_STATE_COMPLETED);
    }

    public function setConfiguredAfterCheckoutOrderStatus(Order $order): void
    {
        $afterCheckoutOrderStatus = $this->pluginConfiguration->getAfterCheckoutOrderStatus();

        $this->orderRepository->saveAfterCheckoutOrderStatus($order, $afterCheckoutOrderStatus);
    }

    public function setConfiguredAfterCheckoutPaymentStatus(Order $order): void
    {
        $afterCheckoutPaymentStatus = $this->pluginConfiguration->getAfterCheckoutPaymentStatus();

        $this->orderRepository->saveAfterCheckoutPaymentStatus($order, $afterCheckoutPaymentStatus);
    }
}
