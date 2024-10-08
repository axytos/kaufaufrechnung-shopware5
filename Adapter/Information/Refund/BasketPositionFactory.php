<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information\Refund;

class BasketPositionFactory
{
    /**
     * @param \Shopware\Models\Order\Detail $invoiceItem
     *
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\BasketPositionInterface
     */
    public function create($invoiceItem)
    {
        return new BasketPosition(
            $invoiceItem
        );
    }

    /**
     * @param \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order $order
     *
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\BasketPositionInterface
     */
    public function createShipping($order)
    {
        return new ShippingBasketPosition($order);
    }

    /**
     * @param \Shopware\Models\Order\Detail[] $invoiceItems
     *
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\BasketPositionInterface[]
     */
    public function createMany($invoiceItems)
    {
        return array_map([$this, 'create'], $invoiceItems);
    }
}
