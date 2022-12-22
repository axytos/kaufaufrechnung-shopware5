<?php

namespace AxytosKaufAufRechnungShopware5\Core;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository;
use AxytosKaufAufRechnungShopware5\DataMapping\BasketDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceBasketDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\CustomerDataDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\DeliveryAddressDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\InvoiceAddressDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\RefundBasketDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\ShippingBasketPositionDtoCollectionFactory;
use AxytosKaufAufRechnungShopware5\ValueCalculation\LogisticianCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\TrackingIdCalculator;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;

class InvoiceOrderContextFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository
     */
    private $orderAttributesRepository;
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\CustomerDataDtoFactory
     */
    private $customerDataDtoFactory;
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\InvoiceAddressDtoFactory
     */
    private $invoiceAddressDtoFactory;
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\DeliveryAddressDtoFactory
     */
    private $deliveryAddressDtoFactory;
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\BasketDtoFactory
     */
    private $basketDtoFactory;
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceBasketDtoFactory
     */
    private $createInvoiceBasketDtoFactory;
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\RefundBasketDtoFactory
     */
    private $refundBasketDtoFactory;
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\ShippingBasketPositionDtoCollectionFactory
     */
    private $shippingBasketPositionDtoCollectionFactory;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\TrackingIdCalculator
     */
    private $trackingIdCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\LogisticianCalculator
     */
    private $logisticianCalculator;


    public function __construct(
        OrderAttributesRepository $orderAttributesRepository,
        CustomerDataDtoFactory $customerDataDtoFactory,
        InvoiceAddressDtoFactory $invoiceAddressDtoFactory,
        DeliveryAddressDtoFactory $deliveryAddressDtoFactory,
        BasketDtoFactory $basketDtoFactory,
        CreateInvoiceBasketDtoFactory $createInvoiceBasketDtoFactory,
        RefundBasketDtoFactory $refundBasketDtoFactory,
        ShippingBasketPositionDtoCollectionFactory $shippingBasketPositionDtoCollectionFactory,
        TrackingIdCalculator $trackingIdCalculator,
        LogisticianCalculator $logisticianCalculator
    ) {
        $this->orderAttributesRepository = $orderAttributesRepository;
        $this->customerDataDtoFactory = $customerDataDtoFactory;
        $this->invoiceAddressDtoFactory = $invoiceAddressDtoFactory;
        $this->deliveryAddressDtoFactory = $deliveryAddressDtoFactory;
        $this->basketDtoFactory = $basketDtoFactory;
        $this->createInvoiceBasketDtoFactory = $createInvoiceBasketDtoFactory;
        $this->refundBasketDtoFactory = $refundBasketDtoFactory;
        $this->shippingBasketPositionDtoCollectionFactory = $shippingBasketPositionDtoCollectionFactory;
        $this->trackingIdCalculator = $trackingIdCalculator;
        $this->logisticianCalculator = $logisticianCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @param \Shopware\Models\Order\Document\Document|null $invoice
     * @param \Shopware\Models\Order\Document\Document|null $creditDocument
     * @return \AxytosKaufAufRechnungShopware5\Core\InvoiceOrderContext
     */
    public function create($order, $invoice = null, $creditDocument = null)
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
            $this->trackingIdCalculator,
            $this->logisticianCalculator
        );
    }
}
