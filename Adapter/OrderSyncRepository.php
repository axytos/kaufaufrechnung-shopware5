<?php

namespace AxytosKaufAufRechnungShopware5\Adapter;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\OrderSyncRepositoryInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderSyncRepository as UnifiedOrderSyncRepository;

class OrderSyncRepository implements OrderSyncRepositoryInterface
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderSyncRepository
     */
    private $unifedOrderSyncRepository;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\PluginOrderFactory
     */
    private $pluginOrderFactory;

    public function __construct(
        UnifiedOrderSyncRepository $unifedOrderSyncRepository,
        PluginOrderFactory $pluginOrderFactory
    ) {
        $this->unifedOrderSyncRepository = $unifedOrderSyncRepository;
        $this->pluginOrderFactory = $pluginOrderFactory;
    }

    /**
     * @param string[] $orderStates
     * @param int|null $limit
     * @param string|null $firstId
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\PluginOrderInterface[]
     */
    public function getOrdersByStates($orderStates, $limit = null, $firstId = null)
    {
        $orders = $this->unifedOrderSyncRepository->getOrdersByStates($orderStates, $limit, $firstId);
        return $this->pluginOrderFactory->createMany($orders);
    }

    /**
     * @param string|int $orderNumber
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\PluginOrderInterface|null
     */
    public function getOrderByOrderNumber($orderNumber)
    {
        $order = $this->unifedOrderSyncRepository->getOrderByOrderNumber(strval($orderNumber));
        if (is_null($order)) {
            return null;
        }
        return $this->pluginOrderFactory->create($order);
    }
}
