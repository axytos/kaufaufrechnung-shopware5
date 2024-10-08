<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\TaxGroupInterface as InvoiceTaxGroupInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\TaxGroupInterface as RefundTaxGroupInterface;
use Shopware\Models\Order\Detail;

class TaxGroup implements InvoiceTaxGroupInterface, RefundTaxGroupInterface
{
    use BasketPositionCalculationTrait;

    /**
     * @var Detail
     */
    private $invoiceItem;

    public function __construct(
        Detail $invoiceItem
    ) {
        $this->invoiceItem = $invoiceItem;
    }

    /**
     * @return float
     */
    public function getTaxPercent()
    {
        return $this->invoiceItem->getTaxRate();
    }

    /**
     * @return float
     */
    public function getValueToTax()
    {
        return $this->calculateNetPrice($this->invoiceItem);
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->calculateTax($this->invoiceItem);
    }
}
