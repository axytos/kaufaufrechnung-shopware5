<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Core;

use AxytosKaufAufRechnungShopware5\DataMapping\BasketDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceBasketDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\CustomerDataDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\DeliveryAddressDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\InvoiceAddressDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\RefundBasketDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\ShippingBasketPositionDtoCollectionFactory;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;

class InvoiceOrderContextFactory
{
    private OrderAttributesRepository $orderAttributesRepository;
    private CustomerDataDtoFactory $customerDataDtoFactory;
    private InvoiceAddressDtoFactory $invoiceAddressDtoFactory;
    private DeliveryAddressDtoFactory $deliveryAddressDtoFactory;
    private BasketDtoFactory $basketDtoFactory;
    private CreateInvoiceBasketDtoFactory $createInvoiceBasketDtoFactory;
    private RefundBasketDtoFactory $refundBasketDtoFactory;
    private ShippingBasketPositionDtoCollectionFactory $shippingBasketPositionDtoCollectionFactory;


    public function __construct(
        OrderAttributesRepository $orderAttributesRepository,
        CustomerDataDtoFactory $customerDataDtoFactory,
        InvoiceAddressDtoFactory $invoiceAddressDtoFactory,
        DeliveryAddressDtoFactory $deliveryAddressDtoFactory,
        BasketDtoFactory $basketDtoFactory,
        CreateInvoiceBasketDtoFactory $createInvoiceBasketDtoFactory,
        RefundBasketDtoFactory $refundBasketDtoFactory,
        ShippingBasketPositionDtoCollectionFactory $shippingBasketPositionDtoCollectionFactory
    ) {
        $this->orderAttributesRepository = $orderAttributesRepository;
        $this->customerDataDtoFactory = $customerDataDtoFactory;
        $this->invoiceAddressDtoFactory = $invoiceAddressDtoFactory;
        $this->deliveryAddressDtoFactory = $deliveryAddressDtoFactory;
        $this->basketDtoFactory = $basketDtoFactory;
        $this->createInvoiceBasketDtoFactory = $createInvoiceBasketDtoFactory;
        $this->refundBasketDtoFactory = $refundBasketDtoFactory;
        $this->shippingBasketPositionDtoCollectionFactory = $shippingBasketPositionDtoCollectionFactory;
    }

    public function create(Order $order, ?Document $invoice = null, ?Document $creditDocument = null): InvoiceOrderContext
    {
        return new InvoiceOrderContext(
            $order,
            $invoice,
            $creditDocument,
            $this->orderAttributesRepository,
            $this->customerDataDtoFactory,
            $this->invoiceAddressDtoFactory,
            $this->deliveryAddressDtoFactory,
            $this->basketDtoFactory,
            $this->createInvoiceBasketDtoFactory,
            $this->refundBasketDtoFactory,
            $this->shippingBasketPositionDtoCollectionFactory,
        );
    }
}
