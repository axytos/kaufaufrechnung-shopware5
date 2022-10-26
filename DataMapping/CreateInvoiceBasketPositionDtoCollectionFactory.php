<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketPositionDtoCollection;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceBasketPositionDtoFactory;
use Shopware\Models\Order\Document\Document;

class CreateInvoiceBasketPositionDtoCollectionFactory
{
    private CreateInvoiceBasketPositionDtoFactory $createInvoiceBasketPositionDtoFactory;

    public function __construct(CreateInvoiceBasketPositionDtoFactory $createInvoiceBasketPositionDtoFactory)
    {
        $this->createInvoiceBasketPositionDtoFactory = $createInvoiceBasketPositionDtoFactory;
    }

    public function create(Document $invoice): CreateInvoiceBasketPositionDtoCollection
    {
        $order = $invoice->getOrder();
        $details = $order->getDetails()->getValues();

        $positions = array_map([$this->createInvoiceBasketPositionDtoFactory, 'create'], $details);
        array_push($positions, $this->createInvoiceBasketPositionDtoFactory->createShippingPosition($order));

        return new CreateInvoiceBasketPositionDtoCollection(...$positions);
    }
}
