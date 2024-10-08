<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information\Refund;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\BasketPositionInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class ShippingBasketPosition implements BasketPositionInterface
{
    /**
     * @var Order
     */
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

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
    public function getNetRefundTotal()
    {
        return $this->order->getInvoiceShippingNet();
    }

    /**
     * @return float
     */
    public function getGrossRefundTotal()
    {
        return $this->order->getInvoiceShipping();
    }
}
