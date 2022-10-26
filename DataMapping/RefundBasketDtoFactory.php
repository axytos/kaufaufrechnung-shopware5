<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketDto;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;

class RefundBasketDtoFactory
{
    private RefundBasketPositionDtoCollectionFactory $positionDtoCollectionFactory;
    private RefundBasketTaxGroupDtoCollectionFactory $taxGroupDtoCollectionFactory;

    public function __construct(
        RefundBasketPositionDtoCollectionFactory $positionDtoCollectionFactory,
        RefundBasketTaxGroupDtoCollectionFactory $taxGroupDtoCollectionFactory
    ) {
        $this->positionDtoCollectionFactory = $positionDtoCollectionFactory;
        $this->taxGroupDtoCollectionFactory = $taxGroupDtoCollectionFactory;
    }

    public function create(Document $creditDocument): RefundBasketDto
    {
        /** @var Order */
        $order = $creditDocument->getOrder();

        $refundBasketDto = new RefundBasketDto();
        $refundBasketDto->grossTotal = floatval($order->getInvoiceAmount());
        $refundBasketDto->netTotal = floatval($order->getInvoiceAmountNet());
        $refundBasketDto->positions = $this->positionDtoCollectionFactory->create($creditDocument);
        $refundBasketDto->taxGroups = $this->taxGroupDtoCollectionFactory->create($creditDocument);
        return $refundBasketDto;
    }
}
