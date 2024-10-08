<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketPositionInterface as UpdateBasketPositionInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\BasketPositionInterface as CheckoutBasketPositionInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\BasketPositionInterface as InvoiceBasketPositionInterface;

class BasketPositionFactory
{
    /**
     * @param \Shopware\Models\Order\Detail $invoiceItem
     *
     * @return InvoiceBasketPositionInterface&UpdateBasketPositionInterface&CheckoutBasketPositionInterface
     */
    public function create($invoiceItem)
    {
        return new BasketPosition(
            $invoiceItem
        );
    }

    /**
     * @param UnifiedShopwareModel\Order $order
     *
     * @return InvoiceBasketPositionInterface&UpdateBasketPositionInterface&CheckoutBasketPositionInterface
     */
    public function createShipping($order)
    {
        return new ShippingBasketPosition(
            $order
        );
    }

    /**
     * @param \Shopware\Models\Order\Detail[] $invoiceItems
     *
     * @return array<InvoiceBasketPositionInterface&UpdateBasketPositionInterface&CheckoutBasketPositionInterface>
     */
    public function createMany($invoiceItems)
    {
        return array_map([$this, 'create'], $invoiceItems);
    }
}
