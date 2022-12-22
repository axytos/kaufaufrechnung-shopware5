<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketDto;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceBasketPositionDtoCollectionFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceTaxGroupDtoCollectionFactory;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;

class CreateInvoiceBasketDtoFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceBasketPositionDtoCollectionFactory
     */
    private $createInvoiceBasketPositionDtoCollectionFactory;
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceTaxGroupDtoCollectionFactory
     */
    private $createInvoiceTaxGroupDtoCollectionFactory;

    public function __construct(
        CreateInvoiceBasketPositionDtoCollectionFactory $createInvoiceBasketPositionDtoCollectionFactory,
        CreateInvoiceTaxGroupDtoCollectionFactory $createInvoiceTaxGroupDtoCollectionFactory
    ) {
        $this->createInvoiceBasketPositionDtoCollectionFactory = $createInvoiceBasketPositionDtoCollectionFactory;
        $this->createInvoiceTaxGroupDtoCollectionFactory = $createInvoiceTaxGroupDtoCollectionFactory;
    }

    /**
     * @param \Shopware\Models\Order\Document\Document $invoice
     * @return \Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketDto
     */
    public function create($invoice)
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
