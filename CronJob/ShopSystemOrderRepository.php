<?php

namespace AxytosKaufAufRechnungShopware5\CronJob;

use Axytos\ECommerce\OrderSync\ShopSystemOrderRepositoryInterface;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;

class ShopSystemOrderRepository implements ShopSystemOrderRepositoryInterface
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderFactory
     */
    private $shopSystemOrderFactory;

    public function __construct(
        OrderRepository $orderRepository,
        ShopSystemOrderFactory $shopSystemOrderFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->shopSystemOrderFactory = $shopSystemOrderFactory;
    }
    /**
     * @return \Axytos\ECommerce\OrderSync\ShopSystemOrderInterface[]
     */
    public function getOrdersToSync()
    {
        $orders = $this->orderRepository->getOrdersToSync();
        return $this->shopSystemOrderFactory->createMany($orders);
    }

    /**
     * @return \Axytos\ECommerce\OrderSync\ShopSystemOrderInterface[]
     */
    public function getOrdersToUpdate()
    {
        return [];
    }
}
