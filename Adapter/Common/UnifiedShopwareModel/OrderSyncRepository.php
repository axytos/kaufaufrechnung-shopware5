<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;

class OrderSyncRepository
{
    /**
     * @var OrderFactory
     */
    private $unifedOrderFactory;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(
        OrderFactory $unifedOrderFactory,
        OrderRepository $orderRepository
    ) {
        $this->unifedOrderFactory = $unifedOrderFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param string[]    $orderStates
     * @param int|null    $limit
     * @param string|null $firstId
     *
     * @return \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order[]
     */
    public function getOrdersByStates($orderStates, $limit = null, $firstId = null)
    {
        $orders = $this->orderRepository->getOrdersByStates($orderStates, $limit, $firstId);

        return $this->createMany($orders);
    }

    /**
     * @param string|int $orderNumber
     *
     * @return Order|null
     */
    public function getOrderByOrderNumber($orderNumber)
    {
        $order = $this->orderRepository->findOrderByOrderNumber(strval($orderNumber));
        if (null === $order) {
            return null;
        }

        return $this->unifedOrderFactory->create($order);
    }

    /**
     * @param \Shopware\Models\Order\Order[] $orders
     *
     * @return \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order[]
     */
    private function createMany($orders)
    {
        return array_map([$this->unifedOrderFactory, 'create'], $orders);
    }
}
