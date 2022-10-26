<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketDto;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceBasketPositionDtoCollectionFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceTaxGroupDtoCollectionFactory;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;

class CreateInvoiceBasketDtoFactory
{
    private CreateInvoiceBasketPositionDtoCollectionFactory $createInvoiceBasketPositionDtoCollectionFactory;
    private CreateInvoiceTaxGroupDtoCollectionFactory $createInvoiceTaxGroupDtoCollectionFactory;

    public function __construct(
        CreateInvoiceBasketPositionDtoCollectionFactory $createInvoiceBasketPositionDtoCollectionFactory,
        CreateInvoiceTaxGroupDtoCollectionFactory $createInvoiceTaxGroupDtoCollectionFactory
    ) {
        $this->createInvoiceBasketPositionDtoCollectionFactory = $createInvoiceBasketPositionDtoCollectionFactory;
        $this->createInvoiceTaxGroupDtoCollectionFactory = $createInvoiceTaxGroupDtoCollectionFactory;
    }

    public function create(Document $invoice): CreateInvoiceBasketDto
    {
        /** @var Order */
        $order = $invoice->getOrder();

        $basket = new CreateInvoiceBasketDto();
        $basket->positions = $this->createInvoiceBasketPositionDtoCollectionFactory->create($invoice);
        $basket->taxGroups = $this->createInvoiceTaxGroupDtoCollectionFactory->create($invoice);
        $basket->grossTotal = floatval($order->getInvoiceAmount());
        $basket->netTotal = floatval($order->getInvoiceAmountNet());
        return $basket;
    }
}
