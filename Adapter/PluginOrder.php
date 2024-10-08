<?php

namespace AxytosKaufAufRechnungShopware5\Adapter;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Model\AxytosOrderStateInfo;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\PluginOrderInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation\HashCalculator;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\BasketUpdateInformation;
use AxytosKaufAufRechnungShopware5\Adapter\Information\CancelInformation;
use AxytosKaufAufRechnungShopware5\Adapter\Information\CheckoutInformation;
use AxytosKaufAufRechnungShopware5\Adapter\Information\InvoiceInformation;
use AxytosKaufAufRechnungShopware5\Adapter\Information\PaymentInformation;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketFactory as RefundBasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Information\RefundInformation;
use AxytosKaufAufRechnungShopware5\Adapter\Information\ShippingInformation;
use AxytosKaufAufRechnungShopware5\Adapter\Information\TrackingInformation;
use Shopware\Models\Order\Status;

class PluginOrder implements PluginOrderInterface
{
    /**
     * @var Order
     */
    private $order;

    /**
     * @var BasketFactory
     */
    private $basketFactory;

    /**
     * @var RefundBasketFactory
     */
    private $refundBasketFactory;

    /**
     * @var HashCalculator
     */
    private $hashCalculator;

    public function __construct(
        Order $order,
        BasketFactory $basketFactory,
        RefundBasketFactory $refundBasketFactory,
        HashCalculator $hashCalculator
    ) {
        $this->order = $order;
        $this->basketFactory = $basketFactory;
        $this->refundBasketFactory = $refundBasketFactory;
        $this->hashCalculator = $hashCalculator;
    }

    /**
     * @return string|int
     */
    public function getOrderNumber()
    {
        return $this->order->getNumber();
    }

    /**
     * @return AxytosOrderStateInfo|null
     */
    public function loadState()
    {
        $attributes = $this->order->getAttributes();

        return new AxytosOrderStateInfo(
            $attributes->getAxytosKaufAufRechnungOrderState(),
            $attributes->getAxytosKaufAufRechnungOrderStateData()
        );
    }

    /**
     * @param string      $state
     * @param string|null $data
     *
     * @return void
     */
    public function saveState($state, $data = null)
    {
        $attributes = $this->order->getAttributes();
        $attributes->setAxytosKaufAufRechnungOrderState($state);
        $attributes->setAxytosKaufAufRechnungOrderStateData($data);
        $attributes->persist();
    }

    /**
     * @return void
     */
    public function freezeBasket()
    {
        $basket = $this->basketFactory->create($this->order);

        $basketHash = $this->hashCalculator->calculateBasketHash($basket);

        $orderAttributes = $this->order->getAttributes();
        $orderAttributes->setAxytosKaufAufRechnungOrderBasketHash($basketHash);
        $orderAttributes->persist();
    }

    public function checkoutInformation()
    {
        return new CheckoutInformation($this->order, $this->basketFactory);
    }

    /**
     * @return bool
     */
    public function hasBeenCanceled()
    {
        return $this->order->isCanceled();
    }

    public function cancelInformation()
    {
        return new CancelInformation($this->order);
    }

    /**
     * @return bool
     */
    public function hasBeenInvoiced()
    {
        // check if order status is completed
        // one order may have multiple invoices
        // when invoices are created with an ERP system and synced back to shopware we cannot know the final number of all invoices
        // so we assume that the order is completely invoiced when:
        // a) there is at least one invoice, because we need the number of the invoice
        // b) the order status is completed

        $invoiceDocument = $this->order->findInvoiceDocument();

        return !is_null($invoiceDocument) && $this->order->isCompleted();
    }

    public function invoiceInformation()
    {
        return new InvoiceInformation($this->order, $this->basketFactory);
    }

    /**
     * @return bool
     */
    public function hasBeenRefunded()
    {
        // disable refund detection for now
        // refund detection does not work reliably because the refund is neither always created in shopware nor synced back from ERP systems
        // need to discuss whether we need this feature or remove it
        return false;
    }

    public function refundInformation()
    {
        return new RefundInformation($this->order, $this->refundBasketFactory);
    }

    /**
     * @return bool
     */
    public function hasShippingReported()
    {
        $orderAttributes = $this->order->getAttributes();

        return $orderAttributes->getAxytosKaufAufRechnungHasShippingReported();
    }

    /**
     * @return bool
     */
    public function hasBeenShipped()
    {
        return $this->order->isCompletelyShipped() || $this->order->isCompleted();
    }

    /**
     * @return void
     */
    public function saveHasShippingReported()
    {
        $orderAttributes = $this->order->getAttributes();
        $orderAttributes->setAxytosKaufAufRechnungHasShippingReported(true);
        $orderAttributes->persist();
    }

    public function shippingInformation()
    {
        return new ShippingInformation($this->order);
    }

    /**
     * @return bool
     */
    public function hasNewTrackingInformation()
    {
        $trackingCode = $this->order->getTrackingCode();

        $orderAttributes = $this->order->getAttributes();
        $reportedTrackingCode = $orderAttributes->getAxytosKaufAufRechnungReportedTrackingCode();

        return $reportedTrackingCode !== $trackingCode;
    }

    /**
     * @return void
     */
    public function saveNewTrackingInformation()
    {
        $trackingCode = $this->order->getTrackingCode();

        $orderAttributes = $this->order->getAttributes();
        $orderAttributes->setAxytosKaufAufRechnungReportedTrackingCode($trackingCode);
        $orderAttributes->persist();
    }

    public function trackingInformation()
    {
        return new TrackingInformation($this->order);
    }

    /**
     * @return bool
     */
    public function hasBasketUpdates()
    {
        $orderAttributes = $this->order->getAttributes();
        $oldHash = $orderAttributes->getAxytosKaufAufRechnungOrderBasketHash();
        $newHash = $this->calculateOrderBasketHash();

        return $oldHash !== $newHash;
    }

    /**
     * @return void
     */
    public function saveBasketUpdatesReported()
    {
        $newHash = $this->calculateOrderBasketHash();
        $orderAttributes = $this->order->getAttributes();
        $orderAttributes->setAxytosKaufAufRechnungOrderBasketHash($newHash);
        $orderAttributes->persist();
    }

    public function basketUpdateInformation()
    {
        return new BasketUpdateInformation($this->order, $this->basketFactory);
    }

    /**
     * @return void
     */
    public function saveHasBeenPaid()
    {
        $this->order->savePaymentStatus(Status::PAYMENT_STATE_COMPLETELY_PAID);
    }

    public function paymentInformation()
    {
        return new PaymentInformation($this->order);
    }

    /**
     * @return string
     */
    private function calculateOrderBasketHash()
    {
        return $this->hashCalculator->calculateBasketHash(
            $this->basketFactory->create($this->order)
        );
    }
}
