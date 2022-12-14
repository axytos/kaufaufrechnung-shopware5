<?php

namespace AxytosKaufAufRechnungShopware5\CronJob;

use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use AxytosKaufAufRechnungShopware5\Core\InvoiceOrderContextFactory;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;

class ShopSystemOrder implements ShopSystemOrderInterface
{
    /**
     * @var \Shopware\Models\Order\Order
     */
    private $order;

    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Core\InvoiceOrderContextFactory
     */
    private $invoiceOrderContextFactory;

    public function __construct(
        Order $order,
        OrderRepository $orderRepository,
        InvoiceOrderContextFactory $invoiceOrderContextFactory
    ) {
        $this->order = $order;
        $this->orderRepository = $orderRepository;
        $this->invoiceOrderContextFactory = $invoiceOrderContextFactory;
    }

    /**
     * @return string|int|null
     */
    public function getOrderNumber()
    {
        return $this->order->getNumber();
    }

    // Transactions

    /**
     * @return void
     */
    public function beginPersistenceTransaction()
    {
        $this->orderRepository->beginTransaction();
    }

    /**
     * @return void
     */
    public function commitPersistenceTransaction()
    {
        $this->orderRepository->commitTransaction();
    }

    /**
     * @return void
     */
    public function rollbackPersistenceTransaction()
    {
        $this->orderRepository->rollbackTransaction();
    }


    // CreateInvoice

    /**
     * @return bool
     */
    public function hasCreateInvoiceReported()
    {
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        return $this->order->getAttribute()->getAxytosKaufAufRechnungHascreateinvoicereported();
    }

    /**
     * @return void
     */
    public function saveHasCreateInvoiceReported()
    {
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        $this->order->getAttribute()->setAxytosKaufAufRechnungHasCreateInvoiceReported(true);
        $this->orderRepository->saveOrder($this->order);
    }

    /**
     * @return bool
     */
    public function hasBeenInvoiced()
    {
        return !is_null($this->getInvoiceDocument());
    }

    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getCreateInvoiceReportData()
    {
        return $this->createInvoiceOrderContext();
    }

    // Cancel

    /**
     * @return bool
     */
    public function hasCancelReported()
    {
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        return $this->order->getAttribute()->getAxytosKaufAufRechnungHasCancelReported();
    }

    /**
     * @return void
     */
    public function saveHasCancelReported()
    {
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        $this->order->getAttribute()->setAxytosKaufAufRechnungHasCancelReported(true);
        $this->orderRepository->saveOrder($this->order);
    }

    /**
     * @return bool
     */
    public function hasBeenCanceled()
    {
        return !is_null($this->getCancellationDocument());
    }

    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getCancelReportData()
    {
        return $this->createInvoiceOrderContext();
    }

    // Refund

    /**
     * @return bool
     */
    public function hasRefundReported()
    {
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        return $this->order->getAttribute()->getAxytosKaufAufRechnungHasRefundReported();
    }

    /**
     * @return void
     */
    public function saveHasRefundReported()
    {
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        $this->order->getAttribute()->setAxytosKaufAufRechnungHasRefundReported(true);
        $this->orderRepository->saveOrder($this->order);
    }

    /**
     * @return bool
     */
    public function hasBeenRefunded()
    {
        return !is_null($this->getCreditDocument());
    }

    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getRefundReportData()
    {
        return $this->createInvoiceOrderContext();
    }

    // Shipping

    /**
     * @return bool
     */
    public function hasShippingReported()
    {
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        return $this->order->getAttribute()->getAxytosKaufAufRechnungHasShippingReported();
    }

    /**
     * @return void
     */
    public function saveHasShippingReported()
    {
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        $this->order->getAttribute()->setAxytosKaufAufRechnungHasShippingReported(true);
        $this->orderRepository->saveOrder($this->order);
    }

    /**
     * @return bool
     */
    public function hasBeenShipped()
    {
        return !is_null($this->getDeliveryNoteDocument());
    }

    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getShippingReportData()
    {
        return $this->createInvoiceOrderContext();
    }

    // Tracking Information

    /**
     * @return bool
     */
    public function hasNewTrackingInformation()
    {
        $trackingCode = $this->order->getTrackingCode();
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        $reportedTrackingCode = $this->order->getAttribute()->getAxytosKaufAufRechnungReportedTrackingCode();
        $result = $reportedTrackingCode != $trackingCode;
        return $result;
    }

    /**
     * @return void
     */
    public function saveNewTrackingInformation()
    {
        $trackingCode = $this->order->getTrackingCode();
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        $this->order->getAttribute()->setAxytosKaufAufRechnungReportedTrackingCode($trackingCode);
        $this->orderRepository->saveOrder($this->order);
    }

    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getNewTrackingInformationReportData()
    {
        return $this->createInvoiceOrderContext();
    }

    // Private Utilities

    /**
     * @return \Shopware\Models\Order\Document\Document|null
     */
    private function getInvoiceDocument()
    {
        return $this->findDocumentByType('invoice');
    }

    /**
     * @return \Shopware\Models\Order\Document\Document|null
     */
    private function getCreditDocument()
    {
        return $this->findDocumentByType('credit');
    }

    /**
     * @return \Shopware\Models\Order\Document\Document|null
     */
    private function getDeliveryNoteDocument()
    {
        return $this->findDocumentByType('delivery_note');
    }

    /**
     * @return \Shopware\Models\Order\Document\Document|null
     */
    private function getCancellationDocument()
    {
        return $this->findDocumentByType('cancellation');
    }

    /**
     * @param string $documentTypeKey
     * @return \Shopware\Models\Order\Document\Document|null
     */
    private function findDocumentByType($documentTypeKey)
    {
        $documents = $this->order->getDocuments();

        foreach ($documents as $document) {
            if ($document->getType()->getKey() === $documentTypeKey) {
                return $document;
            }
        }

        return null;
    }

    /**
     * @return InvoiceOrderContextInterface
     */
    private function createInvoiceOrderContext()
    {
        $invoiceDocument = $this->getInvoiceDocument();
        $creditDocument = $this->getCreditDocument();
        return $this->invoiceOrderContextFactory->create($this->order, $invoiceDocument, $creditDocument);
    }
}
