<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel;

use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository as OldOrderRepository;
use Shopware\Models\Order\Order as ShopwareOrder;
use Shopware\Models\Order\Status;

class Order
{
    /**
     * @var ShopwareOrder
     */
    private $order;

    /**
     * @var OldOrderRepository
     */
    private $orderRepository;

    /**
     * @var ShopwareModelReflector
     */
    private $shopwareModelReflector;

    /**
     * @var PluginConfiguration
     */
    private $pluginConfiguration;

    public function __construct(
        ShopwareOrder $order,
        OldOrderRepository $orderRepository,
        ShopwareModelReflector $shopwareModelReflector,
        PluginConfiguration $pluginConfiguration
    ) {
        $this->order = $order;
        $this->orderRepository = $orderRepository;
        $this->shopwareModelReflector = $shopwareModelReflector;
        $this->pluginConfiguration = $pluginConfiguration;
    }

    /**
     * @return ShopwareOrder
     */
    public function getShopwareOrderObject()
    {
        return $this->order;
    }

    /**
     * @param ShopwareOrder $order
     *
     * @return void
     */
    public function setShopwareOrderObject($order)
    {
        $this->order = $order;
    }

    /**
     * @return void
     */
    public function persist()
    {
        $this->orderRepository->saveOrder($this->order);
    }

    /**
     * @return OrderAttributes
     */
    public function getAttributes()
    {
        return new OrderAttributes(
            $this->order,
            $this->orderRepository
        );
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->order->getCurrency();
    }

    /**
     * @return float
     */
    public function getInvoiceAmount()
    {
        return $this->order->getInvoiceAmount();
    }

    /**
     * @return float
     */
    public function getInvoiceAmountNet()
    {
        return $this->order->getInvoiceAmountNet();
    }

    /**
     * @return float
     */
    public function getInvoiceShipping()
    {
        return $this->order->getInvoiceShipping();
    }

    /**
     * @return float
     */
    public function getInvoiceShippingNet()
    {
        return $this->order->getInvoiceShippingNet();
    }

    /**
     * @return float
     */
    public function getInvoiceShippingTaxRate()
    {
        $taxRate = floatval(0);

        // shopware 5.3 compatibility
        if ($this->shopwareModelReflector->hasMethod($this->order, 'getInvoiceShippingTaxRate')) {
            $taxRate = floatval($this->shopwareModelReflector->callMethod($this->order, 'getInvoiceShippingTaxRate'));
        } else {
            $grossTotal = floatval($this->order->getInvoiceShipping());
            $netTotal = floatval($this->order->getInvoiceShippingNet());
            if ($grossTotal !== floatval(0)) {
                $taxRate = floatval(round((1 - ($netTotal / $grossTotal)) * 100, 2));
            }
        }

        if (is_nan($taxRate) || is_infinite($taxRate)) {
            return floatval(0);
        }

        return $taxRate;
    }

    /**
     * @return string
     */
    public function getTemporaryId()
    {
        return $this->order->getTemporaryId();
    }

    /**
     * @return string|int
     */
    public function getNumber()
    {
        /** @var string|int */
        return $this->order->getNumber();
    }

    /**
     * @return string
     */
    public function getTrackingCode()
    {
        return $this->order->getTrackingCode();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<int,\Shopware\Models\Order\Detail>
     */
    public function getDetails()
    {
        return $this->order->getDetails();
    }

    /**
     * @return \Shopware\Models\Dispatch\Dispatch
     */
    public function getDispatch()
    {
        return $this->order->getDispatch();
    }

    /**
     * @return \Shopware\Models\Customer\Customer|null
     */
    public function getCustomer()
    {
        return $this->order->getCustomer();
    }

    /**
     * @return \Shopware\Models\Order\Billing|null
     */
    public function getBilling()
    {
        return $this->order->getBilling();
    }

    /**
     * @return \Shopware\Models\Order\Shipping|null
     */
    public function getShipping()
    {
        return $this->order->getShipping();
    }

    /**
     * @param int $paymentStatusId
     *
     * @return void
     */
    public function savePaymentStatus($paymentStatusId)
    {
        $this->orderRepository->savePaymentStatus($this->order, $paymentStatusId);
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return Status::ORDER_STATE_COMPLETED === $this->order->getOrderStatus()->getId();
    }

    /**
     * @return bool
     */
    public function isCanceled()
    {
        return Status::ORDER_STATE_CANCELLED === $this->order->getOrderStatus()->getId();
    }

    /**
     * @return bool
     */
    public function isCompletelyShipped()
    {
        return Status::ORDER_STATE_COMPLETELY_DELIVERED === $this->order->getOrderStatus()->getId();
    }

    /**
     * @return OrderDocument|null
     */
    public function findInvoiceDocument()
    {
        $orderDocuments = $this->getDocuments();

        foreach ($orderDocuments as $orderDocument) {
            if ($orderDocument->isInvoiceDocument()) {
                return $orderDocument;
            }
        }

        return null;
    }

    /**
     * @return OrderDocument|null
     */
    public function findCreditDocument()
    {
        $orderDocuments = $this->getDocuments();

        foreach ($orderDocuments as $orderDocument) {
            if ($orderDocument->isCreditDocument()) {
                return $orderDocument;
            }
        }

        return null;
    }

    /**
     * @return OrderDocument|null
     */
    public function findDeliveryNoteDocument()
    {
        $orderDocuments = $this->getDocuments();

        foreach ($orderDocuments as $orderDocument) {
            if ($orderDocument->isDeliveryNoteDocument()) {
                return $orderDocument;
            }
        }

        return null;
    }

    /**
     * @return OrderDocument|null
     */
    public function findCancellationDocument()
    {
        $orderDocuments = $this->getDocuments();

        foreach ($orderDocuments as $orderDocument) {
            if ($orderDocument->isCancellationDocument()) {
                return $orderDocument;
            }
        }

        return null;
    }

    /**
     * @return \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderDocument[]
     */
    private function getDocuments()
    {
        /**
         * @var ?\Doctrine\Common\Collections\ArrayCollection<int,\Shopware\Models\Order\Document\Document>
         *                                                                                                  can be null in some versions of shopware
         */
        $documentCollection = $this->order->getDocuments();

        if (is_null($documentCollection)) {
            return [];
        }

        return array_map([$this, 'createOrderDocument'], $documentCollection->getValues());
    }

    /**
     * @param \Shopware\Models\Order\Document\Document $document
     *
     * @return OrderDocument
     */
    private function createOrderDocument($document)
    {
        return new OrderDocument(
            $document,
            $this->orderRepository,
            $this->shopwareModelReflector,
            $this->pluginConfiguration
        );
    }
}
