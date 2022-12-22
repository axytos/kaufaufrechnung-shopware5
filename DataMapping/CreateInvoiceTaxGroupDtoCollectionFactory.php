<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\CreateInvoiceTaxGroupDto;
use Axytos\ECommerce\DataTransferObjects\CreateInvoiceTaxGroupDtoCollection;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceTaxGroupDtoFactory;
use Shopware\Models\Order\Document\Document;

class CreateInvoiceTaxGroupDtoCollectionFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceTaxGroupDtoFactory
     */
    private $createInvoiceTaxGroupDtoFactory;

    public function __construct(CreateInvoiceTaxGroupDtoFactory $createInvoiceTaxGroupDtoFactory)
    {
        $this->createInvoiceTaxGroupDtoFactory = $createInvoiceTaxGroupDtoFactory;
    }

    /**
     * @param \Shopware\Models\Order\Document\Document $invoice
     * @return \Axytos\ECommerce\DataTransferObjects\CreateInvoiceTaxGroupDtoCollection
     */
    public function create($invoice)
    {
        $order = $invoice->getOrder();
        $details = $order->getDetails()->getValues();

        $positionTaxValues = array_map([$this->createInvoiceTaxGroupDtoFactory, 'create'], $details);
        $positionTaxValues[] = $this->createInvoiceTaxGroupDtoFactory->createShippingTaxGroup($order);

        $taxGroups = array_reduce(
            $positionTaxValues,
            function (array $agg, CreateInvoiceTaxGroupDto $cur) {
                if (array_key_exists("$cur->taxPercent", $agg)) {
                    $agg["$cur->taxPercent"]->total += $cur->total;
                    $agg["$cur->taxPercent"]->valueToTax += $cur->valueToTax;
                } else {
                    $agg["$cur->taxPercent"] = $cur;
                }
                return $agg;
            },
            []
        );
        $taxGroupValues = array_values($taxGroups);

        return new CreateInvoiceTaxGroupDtoCollection(...$taxGroupValues);
    }
}
