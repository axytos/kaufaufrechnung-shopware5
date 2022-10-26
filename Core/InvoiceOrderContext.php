<?php

declare(strict_types=1);

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
use AxytosKaufAufRechnungShopware5\DataMapping\BasketDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\CreateInvoiceBasketDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\CustomerDataDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\DeliveryAddressDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\InvoiceAddressDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\RefundBasketDtoFactory;
use AxytosKaufAufRechnungShopware5\DataMapping\ShippingBasketPositionDtoCollectionFactory;
use DateTime;
use DateTimeInterface;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;

class InvoiceOrderContext implements InvoiceOrderContextInterface
{
    private Order $order;
    private ?Document $invoice;
    private ?Document $creditDocument;
    private OrderAttributesRepository $orderAttributesRepository;
    private CustomerDataDtoFactory $customerDataDtoFactory;
    private InvoiceAddressDtoFactory $invoiceAddressDtoFactory;
    private DeliveryAddressDtoFactory $deliveryAddressDtoFactory;
    private BasketDtoFactory $basketDtoFactory;
    private CreateInvoiceBasketDtoFactory $createInvoiceBasketDtoFactory;
    private RefundBasketDtoFactory $refundBasketDtoFactory;
    private ShippingBasketPositionDtoCollectionFactory $shippingBasketPositionDtoCollectionFactory;

    private string $invoiceNumber;

    public function __construct(
        Order $order,
        ?Document $invoice,
        ?Document $creditDocument,
        OrderAttributesRepository $orderAttributesRepository,
        CustomerDataDtoFactory $customerDataDtoFactory,
        InvoiceAddressDtoFactory $invoiceAddressDtoFactory,
        DeliveryAddressDtoFactory $deliveryAddressDtoFactory,
        BasketDtoFactory $basketDtoFactory,
        CreateInvoiceBasketDtoFactory $createInvoiceBasketDtoFactory,
        RefundBasketDtoFactory $refundBasketDtoFactory,
        ShippingBasketPositionDtoCollectionFactory $shippingBasketPositionDtoCollectionFactory
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
    }

    public function getOrderNumber(): string
    {
        return strval($this->order->getNumber());
    }

    public function getOrderInvoiceNumber(): string
    {
        if (!is_null($this->invoice)) {
            /** @phpstan-ignore-next-line */
            return $this->invoice->getDocumentId();
        }
        return $this->invoiceNumber;
    }

    public function setOrderInvoiceNumber(string $invoiceNumber): void
    {
        $this->invoiceNumber = $invoiceNumber;
    }

    public function getOrderDateTime(): DateTimeInterface
    {
        return new DateTime();
    }

    public function getPersonalData(): CustomerDataDto
    {
        return $this->customerDataDtoFactory->create($this->order);
    }

    public function getInvoiceAddress(): InvoiceAddressDto
    {
        return $this->invoiceAddressDtoFactory->create($this->order);
    }

    public function getDeliveryAddress(): DeliveryAddressDto
    {
        return $this->deliveryAddressDtoFactory->create($this->order);
    }

    public function getBasket(): BasketDto
    {
        return $this->basketDtoFactory->create($this->order);
    }

    public function getRefundBasket(): RefundBasketDto
    {
        /** @phpstan-ignore-next-line */
        return $this->refundBasketDtoFactory->create($this->creditDocument);
    }

    public function getCreateInvoiceBasket(): CreateInvoiceBasketDto
    {
        if (is_null($this->invoice)) {
            return new CreateInvoiceBasketDto();
        }

        return $this->createInvoiceBasketDtoFactory->create($this->invoice);
    }

    public function getShippingBasketPositions(): ShippingBasketPositionDtoCollection
    {
        return $this->shippingBasketPositionDtoCollectionFactory->create($this->order);
    }

    public function getPreCheckResponseData(): array
    {
        return $this->orderAttributesRepository->loadPreCheckResponseData($this->order);
    }

    public function setPreCheckResponseData(array $data): void
    {
        $this->orderAttributesRepository->persistPreCheckResponseData($this->order, $data);
    }

    public function getReturnPositions(): ReturnPositionModelDtoCollection
    {
        return new ReturnPositionModelDtoCollection();
    }
}
