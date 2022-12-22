<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketDto;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;

class RefundBasketDtoFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\RefundBasketPositionDtoCollectionFactory
     */
    private $positionDtoCollectionFactory;
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\RefundBasketTaxGroupDtoCollectionFactory
     */
    private $taxGroupDtoCollectionFactory;

    public function __construct(
        RefundBasketPositionDtoCollectionFactory $positionDtoCollectionFactory,
        RefundBasketTaxGroupDtoCollectionFactory $taxGroupDtoCollectionFactory
    ) {
        $this->positionDtoCollectionFactory = $positionDtoCollectionFactory;
        $this->taxGroupDtoCollectionFactory = $taxGroupDtoCollectionFactory;
    }

    /**
     * @param \Shopware\Models\Order\Document\Document $creditDocument
     * @return \Axytos\ECommerce\DataTransferObjects\RefundBasketDto
     */
    public function create($creditDocument)
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
