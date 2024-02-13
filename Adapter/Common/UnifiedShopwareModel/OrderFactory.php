<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel;

use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository as OldOrderRepository;

class OrderFactory
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
        OldOrderRepository $orderRepository,
        PluginConfiguration $pluginConfiguration
    ) {
        $this->orderRepository = $orderRepository;
        $this->pluginConfiguration = $pluginConfiguration;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order
     */
    public function create($order)
    {
        return new Order(
            $order,
            $this->orderRepository,
            new ShopwareModelReflector(),
            $this->pluginConfiguration
        );
    }

    /**
     * @param \Shopware\Models\Order\Order[] $orders
     * @return \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order[]
     */
    public function createMany($orders)
    {
        return array_map([$this, 'create'], $orders);
    }
}
