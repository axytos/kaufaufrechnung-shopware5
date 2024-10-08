<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketPositionInterface as UpdateBasketPositionInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\BasketPositionInterface as CheckoutBasketPositionInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\BasketPositionInterface as InvoiceBasketPositionInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class ShippingBasketPosition implements InvoiceBasketPositionInterface, UpdateBasketPositionInterface, CheckoutBasketPositionInterface
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
     * @return string
     */
    public function getProductNumber()
    {
        return '0';
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return 'Shipping';
    }

    /**
     * @return string|null
     */
    public function getProductCategory()
    {
        return null;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return 1;
    }

    /**
     * @return float
     */
    public function getTaxPercent()
    {
        return $this->order->getInvoiceShippingTaxRate();
    }

    /**
     * @return float
     */
    public function getNetPricePerUnit()
    {
        return $this->order->getInvoiceShippingNet();
    }

    /**
     * @return float
     */
    public function getGrossPricePerUnit()
    {
        return $this->order->getInvoiceShipping();
    }

    /**
     * @return float
     */
    public function getNetPositionTotal()
    {
        return $this->order->getInvoiceShippingNet();
    }

    /**
     * @return float
     */
    public function getGrossPositionTotal()
    {
        return $this->order->getInvoiceShipping();
    }
}
