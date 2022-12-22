<?php

namespace AxytosKaufAufRechnungShopware5\Core;

use Axytos\ECommerce\Order\OrderCheckProcessStates;
use Shopware\Models\Order\Order;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository;

class OrderCheckProcessStateMachine
{
    /**
     * @param \Shopware\Models\Order\Order $order
     * @return string|null
     */
    public function getState($order)
    {
        /** @var OrderAttributesRepository $orderAttributesRepository */
        $orderAttributesRepository = Shopware()->Container()->get(OrderAttributesRepository::class);
        return $orderAttributesRepository->loadOrderProcessState($order);
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return void
     */
    public function setUnchecked($order)
    {
        $this->updateState($order, OrderCheckProcessStates::UNCHECKED);
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return void
     */
    public function setChecked($order)
    {
        $this->updateState($order, OrderCheckProcessStates::CHECKED);
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return void
     */
    public function setConfirmed($order)
    {
        $this->updateState($order, OrderCheckProcessStates::CONFIRMED);
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return void
     */
    public function setFailed($order)
    {
        $this->updateState($order, OrderCheckProcessStates::FAILED);
    }

    /**
     * @return void
     * @param string $orderCheckProcessState
     */
    private function updateState(Order $order, $orderCheckProcessState)
    {
        $orderCheckProcessState = (string) $orderCheckProcessState;
        /** @var OrderAttributesRepository $orderAttributesRepository */
        $orderAttributesRepository = Shopware()->Container()->get(OrderAttributesRepository::class);
        $orderAttributesRepository->persistOrderProcessState($order, $orderCheckProcessState);
    }
}
