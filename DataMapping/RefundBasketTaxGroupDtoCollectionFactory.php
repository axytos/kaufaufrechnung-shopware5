<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketTaxGroupDto;
use Axytos\ECommerce\DataTransferObjects\RefundBasketTaxGroupDtoCollection;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Detail as OrderDetail;

class RefundBasketTaxGroupDtoCollectionFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\RefundBasketTaxGroupDtoFactory
     */
    private $taxGroupDtoFactory;

    public function __construct(RefundBasketTaxGroupDtoFactory $taxGroupDtoFactory)
    {
        $this->taxGroupDtoFactory = $taxGroupDtoFactory;
    }

    /**
     * @param \Shopware\Models\Order\Document\Document $creditDocument
     * @return \Axytos\ECommerce\DataTransferObjects\RefundBasketTaxGroupDtoCollection
     */
    public function create($creditDocument)
    {
        $order = $creditDocument->getOrder();
        /** @var OrderDetail[] */
        $details = $order->getDetails()->toArray();

        $positionTaxValues = array_map([$this->taxGroupDtoFactory, 'create'], $details);
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
