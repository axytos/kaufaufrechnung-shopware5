<?php

namespace AxytosKaufAufRechnungShopware5\CronJob;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;

class OrderSyncItemRepository
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderRepository
     */
     private $shopSystemOrderRepository;

   /**
    * @var \AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemFactory
    */
    private $orderSyncItemFactory;

    public function __construct(
        ShopSystemOrderRepository $shopSystemOrderRepository,
        OrderSyncItemFactory $orderSyncItemFactory
    ) {
        $this->shopSystemOrderRepository = $shopSystemOrderRepository;
        $this->orderSyncItemFactory = $orderSyncItemFactory;
    }
    /**
     * @return OrderSyncItemInterface[]
     */
    public function getOrdersToSync()
    {
        $shopSystemOrders = $this->shopSystemOrderRepository->getOrdersToSync();
        return $this->orderSyncItemFactory->createMany($shopSystemOrders);
    }
}
