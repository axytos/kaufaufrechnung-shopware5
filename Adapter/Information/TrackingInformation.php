<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\TrackingInformationInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Tracking\DeliveryAddress;

class TrackingInformation implements TrackingInformationInterface
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order
     */
    private $order;

    public function __construct(
        Order $order
    ) {
        $this->order = $order;
    }

    /**
     * @return string|int
     */
    public function getOrderNumber()
    {
        return $this->order->getNumber();
    }

    /**
     * @return float
     */
    public function getDeliveryWeight()
    {
        // for now delivery weight is not important for risk evaluation
        // because different shop systems don't always provide the necessary
        // information to accurately the exact delivery weight for each delivery
        // we decided to return 0 as constant delivery weight
        return 0;
    }

    /**
     * @return string
     */
    public function getDeliveryMethod()
    {
        $dispatch = $this->order->getDispatch();
        return $dispatch->getName();
    }

    /**
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Tracking\DeliveryAddressInterface
     */
    public function getDeliveryAddress()
    {
        return new DeliveryAddress($this->order->getShipping());
    }

    /**
     * @return string[]
     */
    public function getTrackingIds()
    {
        $trackingCode = strval($this->order->getTrackingCode());

        if ($trackingCode !== '') {
            return [$trackingCode];
        }

        return [];
    }
}
