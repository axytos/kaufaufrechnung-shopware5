<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\TaxGroupInterface as InvoiceTaxGroupInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\TaxGroupInterface as RefundTaxGroupInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class ShippingTaxGroup implements InvoiceTaxGroupInterface, RefundTaxGroupInterface
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
     * @return float
     */
    public function getTaxPercent()
    {
        return $this->order->getInvoiceShippingTaxRate();
    }

    /**
     * @return float
     */
    public function getValueToTax()
    {
        return $this->order->getInvoiceShippingNet();
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return round($this->order->getInvoiceShipping() - $this->order->getInvoiceShippingNet(), 2);
    }
}
