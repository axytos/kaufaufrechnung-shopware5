<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketPositionDtoCollection;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceBasketPositionDtoFactory;
use Shopware\Models\Order\Document\Document;

class CreateInvoiceBasketPositionDtoCollectionFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceBasketPositionDtoFactory
     */
    private $createInvoiceBasketPositionDtoFactory;

    public function __construct(CreateInvoiceBasketPositionDtoFactory $createInvoiceBasketPositionDtoFactory)
    {
        $this->createInvoiceBasketPositionDtoFactory = $createInvoiceBasketPositionDtoFactory;
    }

    /**
     * @param \Shopware\Models\Order\Document\Document $invoice
     * @return \Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketPositionDtoCollection
     */
    public function create($invoice)
    {
        $order = $invoice->getOrder();
        $details = $order->getDetails()->getValues();

        $positions = array_map([$this->createInvoiceBasketPositionDtoFactory, 'create'], $details);
        array_push($positions, $this->createInvoiceBasketPositionDtoFactory->createShippingPosition($order));

        return new CreateInvoiceBasketPositionDtoCollection(...$positions);
    }
}
