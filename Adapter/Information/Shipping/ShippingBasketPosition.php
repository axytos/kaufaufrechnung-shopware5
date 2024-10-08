<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information\Shipping;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Shipping\BasketPositionInterface;

class ShippingBasketPosition implements BasketPositionInterface
{
    /**
     * @return string
     */
    public function getProductNumber()
    {
        return '0';
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return 1;
    }
}
