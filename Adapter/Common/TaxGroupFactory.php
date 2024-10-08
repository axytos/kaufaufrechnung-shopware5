<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\TaxGroupInterface as InvoiceTaxGroupInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\TaxGroupInterface as RefundTaxGroupInterface;

class TaxGroupFactory
{
    /**
     * @param \Shopware\Models\Order\Detail $invoiceItem
     *
     * @return InvoiceTaxGroupInterface&RefundTaxGroupInterface
     */
    public function create($invoiceItem)
    {
        return new TaxGroup(
            $invoiceItem
        );
    }

    /**
     * @param UnifiedShopwareModel\Order $order
     *
     * @return InvoiceTaxGroupInterface&RefundTaxGroupInterface
     */
    public function createShipping($order)
    {
        return new ShippingTaxGroup(
            $order
        );
    }

    /**
     * @param \Shopware\Models\Order\Detail[] $invoiceItems
     *
     * @return array<InvoiceTaxGroupInterface&RefundTaxGroupInterface>
     */
    public function createMany($invoiceItems)
    {
        return array_map([$this, 'create'], $invoiceItems);
    }

    /**
     * @param array<InvoiceTaxGroupInterface&RefundTaxGroupInterface> $taxGroups
     *
     * @return array<InvoiceTaxGroupInterface&RefundTaxGroupInterface>
     */
    public function combineTaxGroups($taxGroups)
    {
        /** @var array<string, CombinedTaxGroup> */
        $combinedTaxGroups = array_reduce(
            $taxGroups,
            function ($agg, $cur) {
                /**
                 * @var array<string, CombinedTaxGroup>                  $agg
                 * @var InvoiceTaxGroupInterface&RefundTaxGroupInterface $cur
                 */
                $taxPercent = $cur->getTaxPercent();
                $taxPercentKey = "{$taxPercent}";
                if (array_key_exists($taxPercentKey, $agg)) {
                    $agg[$taxPercentKey]->addTaxGroup($cur);
                } else {
                    $agg[$taxPercentKey] = new CombinedTaxGroup($cur);
                }

                return $agg;
            },
            []
        );

        return array_values($combinedTaxGroups);
    }
}
