<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Subscriber;

use AxytosKaufAufRechnungShopware5\Core\InvoiceOrderContextFactory;
use Shopware\Components\ContainerAwareEventManager;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;

class CustomEventDispatcher
{
    private ContainerAwareEventManager $eventManager;
    private InvoiceOrderContextFactory $invoiceOrderContextFactory;

    public function __construct(
        ContainerAwareEventManager $eventManager,
        InvoiceOrderContextFactory $invoiceOrderContextFactory
    ) {
        $this->eventManager = $eventManager;
        $this->invoiceOrderContextFactory = $invoiceOrderContextFactory;
    }

    public function dispatchCreateInvoice(Order $order, Document $invoice): void
    {
        $invoiceOrderContext = $this->invoiceOrderContextFactory->create($order, $invoice);

        $this->eventManager->notify(CustomEventNames::CREATE_INVOICE, [
            "AxytosInvoiceOrderContext" => $invoiceOrderContext
        ]);
    }

    public function dispatchRefundOrder(Order $order, Document $credit): void
    {
        /** @var Document */
        $invoice = $this->getDocument($order, 'invoice');
        $invoiceOrderContext = $this->invoiceOrderContextFactory->create($order, $invoice, $credit);

        $this->eventManager->notify(CustomEventNames::REFUND_ORDER, [
            "AxytosInvoiceOrderContext" => $invoiceOrderContext
        ]);
    }

    private function getDocument(Order $order, string $key): ?Document
    {
        foreach ($order->getDocuments() as $document) {
            if ($document->getType()->getKey() === $key) {
                return $document;
            }
        }

        return null;
    }
}
