<?php

namespace AxytosKaufAufRechnungShopware5\Core;

use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

class OrderStateMachine
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository
     */
    private $orderRepository;
    /**
     * @var \AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration
     */
    private $pluginConfiguration;

    public function __construct(
        OrderRepository $orderRepository,
        PluginConfiguration $pluginConfiguration
    ) {
        $this->orderRepository = $orderRepository;
        $this->pluginConfiguration = $pluginConfiguration;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return void
     */
    public function setConfiguredAfterCheckoutOrderStatus($order)
    {
        $afterCheckoutOrderStatus = $this->pluginConfiguration->getAfterCheckoutOrderStatus();

        $this->orderRepository->saveAfterCheckoutOrderStatus($order, $afterCheckoutOrderStatus);
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return void
     */
    public function setConfiguredAfterCheckoutPaymentStatus($order)
    {
        $afterCheckoutPaymentStatus = $this->pluginConfiguration->getAfterCheckoutPaymentStatus();

        $this->orderRepository->saveAfterCheckoutPaymentStatus($order, $afterCheckoutPaymentStatus);
    }
}
