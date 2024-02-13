<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel;

use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;

class OrderDocumentType
{
    /**
     * @var \Shopware\Models\Document\Document
     *      In Shopware 5.3: \Shopware\Models\Order\Document\Type
     */
    private $documentType;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\ShopwareModelReflector
     */
    private $shopwareModelReflector;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration
     */
    private $pluginConfiguration;

    /**
     * @param \Shopware\Models\Document\Document $document
     *        In Shopware 5.3: \Shopware\Models\Order\Document\Type
     */
    public function __construct(
        $document,
        ShopwareModelReflector $shopwareModelReflector,
        PluginConfiguration $pluginConfiguration
    ) {
        $this->documentType = $document;
        $this->shopwareModelReflector = $shopwareModelReflector;
        $this->pluginConfiguration = $pluginConfiguration;
    }

    /**
     * @return string|null
     */
    public function getKey()
    {
        if ($this->shopwareModelReflector->hasMethod($this->documentType, 'getKey')) {
            $key = strval($this->shopwareModelReflector->callMethod($this->documentType, 'getKey'));

            if ($key !== '') {
                return $key;
            }
        }

        // For Shopware < 5.5
        return $this->inferKeyFromDocumentName();
    }



    /**
     * @return string|null
     */
    private function inferKeyFromDocumentName()
    {
        $patterns = [
            $this->pluginConfiguration->getInvoiceDocumentKey() => '/^rechnung|invoice/i',
            'credit' => '/gutschrift|credit/i',
            'delivery_note' => '/lieferschein|delivery/i',
            'cancellation' => '/storno|cancellation/i'
        ];

        $name = $this->documentType->getName();
        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $name) === 1) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isInvoiceDocument()
    {
        return $this->getKey() === $this->pluginConfiguration->getInvoiceDocumentKey();
    }

    /**
     * @return bool
     */
    public function isCreditDocument()
    {
        return $this->getKey() === 'credit';
    }

    /**
     * @return bool
     */
    public function isDeliveryNoteDocument()
    {
        return $this->getKey() === 'delivery_note';
    }

    /**
     * @return bool
     */
    public function isCancellationDocument()
    {
        return $this->getKey() === 'cancellation';
    }
}
