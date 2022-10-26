<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\CreateInvoiceTaxGroupDto;
use Axytos\ECommerce\DataTransferObjects\CreateInvoiceTaxGroupDtoCollection;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceTaxGroupDtoFactory;
use Shopware\Models\Order\Document\Document;

class CreateInvoiceTaxGroupDtoCollectionFactory
{
    private CreateInvoiceTaxGroupDtoFactory $createInvoiceTaxGroupDtoFactory;

    public function __construct(CreateInvoiceTaxGroupDtoFactory $createInvoiceTaxGroupDtoFactory)
    {
        $this->createInvoiceTaxGroupDtoFactory = $createInvoiceTaxGroupDtoFactory;
    }

    public function create(Document $invoice): CreateInvoiceTaxGroupDtoCollection
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
