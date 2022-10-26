<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Core;

use Axytos\ECommerce\Order\OrderCheckProcessStates;
use Shopware\Models\Order\Order;
use AxytosKaufAufRechnungShopware5\Core\OrderAttributesRepository;

class OrderCheckProcessStateMachine
{
    public function getState(Order $order): ?string
    {
        /** @var OrderAttributesRepository $orderAttributesRepository */
        $orderAttributesRepository = Shopware()->Container()->get(OrderAttributesRepository::class);
        return $orderAttributesRepository->loadOrderProcessState($order);
    }

    public function setUnchecked(Order $order): void
    {
        $this->updateState($order, OrderCheckProcessStates::UNCHECKED);
    }

    public function setChecked(Order $order): void
    {
        $this->updateState($order, OrderCheckProcessStates::CHECKED);
    }

    public function setConfirmed(Order $order): void
    {
        $this->updateState($order, OrderCheckProcessStates::CONFIRMED);
    }

    public function setFailed(Order $order): void
    {
        $this->updateState($order, OrderCheckProcessStates::FAILED);
    }

    private function updateState(Order $order, string $orderCheckProcessState): void
    {
        /** @var OrderAttributesRepository $orderAttributesRepository */
        $orderAttributesRepository = Shopware()->Container()->get(OrderAttributesRepository::class);
        $orderAttributesRepository->persistOrderProcessState($order, $orderCheckProcessState);
    }
}
