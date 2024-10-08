<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Shipping\BasketPositionInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\ShippingInformationInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Shipping\BasketPosition;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Shipping\ShippingBasketPosition;

class ShippingInformation implements ShippingInformationInterface
{
    /**
     * @var Order
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
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Shipping\BasketPositionInterface[]
     */
    public function getShippingBasketPositions()
    {
        /** @var BasketPositionInterface[] */
        $positions = array_map(
            [$this, 'createBasketPosition'],
            $this->order->getDetails()->getValues()
        );
        $positions[] = new ShippingBasketPosition();

        return $positions;
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     *
     * @return BasketPositionInterface
     */
    private function createBasketPosition($detail)
    {
        return new BasketPosition($detail);
    }
}
