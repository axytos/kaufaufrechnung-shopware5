<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\TaxGroupInterface as InvoiceTaxGroupInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\TaxGroupInterface as RefundTaxGroupInterface;

class CombinedTaxGroup implements InvoiceTaxGroupInterface, RefundTaxGroupInterface
{
    /**
     * @var array<InvoiceTaxGroupInterface&RefundTaxGroupInterface>
     */
    private $taxGroups;

    /**
     * @param InvoiceTaxGroupInterface&RefundTaxGroupInterface $taxGroup
     */
    public function __construct($taxGroup)
    {
        $this->taxGroups = [$taxGroup];
    }

    /**
     * @param InvoiceTaxGroupInterface&RefundTaxGroupInterface $taxGroup
     *
     * @return void
     */
    public function addTaxGroup($taxGroup)
    {
        $this->taxGroups[] = $taxGroup;
    }

    /**
     * @return float
     */
    public function getTaxPercent()
    {
        return $this->taxGroups[0]->getTaxPercent();
    }

    /**
     * @return float
     */
    public function getValueToTax()
    {
        $valueToTax = array_reduce(
            $this->taxGroups,
            function ($sum, $taxGroup) {
                /**
                 * @var float                                            $sum
                 * @var InvoiceTaxGroupInterface&RefundTaxGroupInterface $taxGroup
                 */
                return $sum + $taxGroup->getValueToTax();
            },
            0.0
        );

        return round($valueToTax, 2);
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        $total = array_reduce(
            $this->taxGroups,
            function ($sum, $taxGroup) {
                /**
                 * @var float                                            $sum
                 * @var InvoiceTaxGroupInterface&RefundTaxGroupInterface $taxGroup
                 */
                return $sum + $taxGroup->getTotal();
            },
            0.0
        );

        return round($total, 2);
    }
}
