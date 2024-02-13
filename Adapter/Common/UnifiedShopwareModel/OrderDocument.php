<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel;

use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use Shopware\Models\Order\Document\Document as ShopwareOrderDocument;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository as OldOrderRepository;

class OrderDocument
{
    /**
     * @var \Shopware\Models\Order\Document\Document
     */
    private $document;

    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\ShopwareModelReflector
     */
    private $shopwareModelReflector;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration
     */
    private $pluginConfiguration;

    public function __construct(
        ShopwareOrderDocument $document,
        OldOrderRepository $orderRepository,
        ShopwareModelReflector $shopwareModelReflector,
        PluginConfiguration $pluginConfiguration
    ) {
        $this->document = $document;
        $this->orderRepository = $orderRepository;
        $this->shopwareModelReflector = $shopwareModelReflector;
        $this->pluginConfiguration = $pluginConfiguration;
    }

    /**
     * @return string
     */
    public function getDocumentId()
    {
        return strval($this->document->getDocumentId());
    }

    /**
     * @return \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order
     */
    public function getOrder()
    {
        return new Order(
            $this->document->getOrder(),
            $this->orderRepository,
            $this->shopwareModelReflector,
            $this->pluginConfiguration
        );
    }

    /**
     * @return bool
     */
    public function isInvoiceDocument()
    {
        return $this->getType()->isInvoiceDocument();
    }

    /**
     * @return bool
     */
    public function isCreditDocument()
    {
        return $this->getType()->isCreditDocument();
    }

    /**
     * @return bool
     */
    public function isDeliveryNoteDocument()
    {
        return $this->getType()->isDeliveryNoteDocument();
    }

    /**
     * @return bool
     */
    public function isCancellationDocument()
    {
        return $this->getType()->isCancellationDocument();
    }

    /**
     * @return \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderDocumentType
     */
    private function getType()
    {
        return new OrderDocumentType(
            $this->document->getType(),
            $this->shopwareModelReflector,
            $this->pluginConfiguration
        );
    }
}
