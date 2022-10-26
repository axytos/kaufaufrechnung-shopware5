<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketTaxGroupDto;
use Axytos\ECommerce\DataTransferObjects\RefundBasketTaxGroupDtoCollection;
use Shopware\Models\Order\Document\Document;

class RefundBasketTaxGroupDtoCollectionFactory
{
    private RefundBasketTaxGroupDtoFactory $taxGroupDtoFactory;

    public function __construct(RefundBasketTaxGroupDtoFactory $taxGroupDtoFactory)
    {
        $this->taxGroupDtoFactory = $taxGroupDtoFactory;
    }

    public function create(Document $creditDocument): RefundBasketTaxGroupDtoCollection
    {
        $order = $creditDocument->getOrder();
        $details = $order->getDetails();

        $positionTaxValues = array_map([$this->taxGroupDtoFactory, 'create'], $details->toArray());
        $positionTaxValues[] = $this->taxGroupDtoFactory->createShippingTaxGroup($order);

        $taxGroups = array_reduce(
            $positionTaxValues,
            function (array $agg, RefundBasketTaxGroupDto $cur) {
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

        return new RefundBasketTaxGroupDtoCollection(...$taxGroupValues);
    }
}
