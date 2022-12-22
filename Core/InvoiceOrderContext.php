<?php

namespace AxytosKaufAufRechnungShopware5\Core;

use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use Axytos\ECommerce\DataTransferObjects\BasketDto;
use Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketDto;
use Axytos\ECommerce\DataTransferObjects\CustomerDataDto;
use Axytos\ECommerce\DataTransferObjects\DeliveryAddressDto;
use Axytos\ECommerce\DataTransferObjects\InvoiceAddressDto;
use Axytos\ECommerce\DataTransferObjects\RefundBasketDto;
use Axytos\ECommerce\DataTransferObjects\ReturnPositionModelDtoCollection;
use Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDtoCollection;
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
use DateTime;
use DateTimeInterface;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;

class InvoiceOrderContext implements InvoiceOrderContextInterface
{
    /**
     * @var \Shopware\Models\Order\Order
     */
    private $order;
    /**
     * @var \Shopware\Models\Order\Document\Document|null
     */
    private $invoice;
    /**
     * @var \Shopware\Models\Order\Document\Document|null
     */
    private $creditDocument;
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

    /**
     * @var string
     */
    private $invoiceNumber;

    /**
     * @param \Shopware\Models\Order\Document\Document|null $invoice
     * @param \Shopware\Models\Order\Document\Document|null $creditDocument
     */
    public function __construct(
        Order $order,
        $invoice,
        $creditDocument,
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
        $this->order = $order;
        $this->invoice = $invoice;
        $this->creditDocument = $creditDocument;
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
     * @return string
     */
    public function getOrderNumber()
    {
        return strval($this->order->getNumber());
    }

    /**
     * @return string
     */
    public function getOrderInvoiceNumber()
    {
        if (!is_null($this->invoice)) {
            /** @phpstan-ignore-next-line */
            return $this->invoice->getDocumentId();
        }
        return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     * @return void
     */
    public function setOrderInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getOrderDateTime()
    {
        return new DateTime();
    }

    /**
     * @return \Axytos\ECommerce\DataTransferObjects\CustomerDataDto
     */
    public function getPersonalData()
    {
        return $this->customerDataDtoFactory->create($this->order);
    }

    /**
     * @return \Axytos\ECommerce\DataTransferObjects\InvoiceAddressDto
     */
    public function getInvoiceAddress()
    {
        return $this->invoiceAddressDtoFactory->create($this->order);
    }

    /**
     * @return \Axytos\ECommerce\DataTransferObjects\DeliveryAddressDto
     */
    public function getDeliveryAddress()
    {
        return $this->deliveryAddressDtoFactory->create($this->order);
    }

    /**
     * @return \Axytos\ECommerce\DataTransferObjects\BasketDto
     */
    public function getBasket()
    {
        return $this->basketDtoFactory->create($this->order);
    }

    /**
     * @return \Axytos\ECommerce\DataTransferObjects\RefundBasketDto
     */
    public function getRefundBasket()
    {
        /** @phpstan-ignore-next-line */
        return $this->refundBasketDtoFactory->create($this->creditDocument);
    }

    /**
     * @return \Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketDto
     */
    public function getCreateInvoiceBasket()
    {
        if (is_null($this->invoice)) {
            return new CreateInvoiceBasketDto();
        }

        return $this->createInvoiceBasketDtoFactory->create($this->invoice);
    }

    /**
     * @return \Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDtoCollection
     */
    public function getShippingBasketPositions()
    {
        return $this->shippingBasketPositionDtoCollectionFactory->create($this->order);
    }

    /**
     * @return mixed[]
     */
    public function getPreCheckResponseData()
    {
        return $this->orderAttributesRepository->loadPreCheckResponseData($this->order);
    }

    /**
     * @param mixed[] $data
     * @return void
     */
    public function setPreCheckResponseData($data)
    {
        $this->orderAttributesRepository->persistPreCheckResponseData($this->order, $data);
    }

    /**
     * @return \Axytos\ECommerce\DataTransferObjects\ReturnPositionModelDtoCollection
     */
    public function getReturnPositions()
    {
        return new ReturnPositionModelDtoCollection();
    }

    /**
     * @return float
     */
    public function getDeliveryWeight()
    {
        // for now delivery weight is not important for risk evaluation
        // because different shop systems don't always provide the necessary
        // information to accurately the exact delivery weight for each delivery
        // we decided to return 0 as constant delivery weight
        return 0;
    }

    /**
     * @return string[]
     */
    public function getTrackingIds()
    {
        return $this->trackingIdCalculator->calculate($this->order);
    }

    /**
     * @return string
     */
    public function getLogistician()
    {
        return $this->logisticianCalculator->calculate($this->order);
    }
}
