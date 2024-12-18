<?php

namespace AxytosKaufAufRechnungShopware5\Configuration;

use Shopware\Components\Plugin\DBALConfigReader;

class PluginConfiguration
{
    /**
     * @return string
     */
    public function getApiHost()
    {
        return strval($this->getSettingsValue(PluginConfigurationValueNames::API_HOST));
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return strval($this->getSettingsValue(PluginConfigurationValueNames::API_KEY));
    }

    /**
     * @return string|null
     */
    public function getClientSecret()
    {
        return $this->getSettingsValue(PluginConfigurationValueNames::CLIENT_SECRET);
    }

    /**
     * @return AfterCheckoutOrderStatus
     */
    public function getAfterCheckoutOrderStatus()
    {
        $value = $this->getSettingsValue(PluginConfigurationValueNames::AFTER_CHECKOUT_ORDER_STATUS);

        return new AfterCheckoutOrderStatus($value);
    }

    /**
     * @return AfterCheckoutPaymentStatus
     */
    public function getAfterCheckoutPaymentStatus()
    {
        $value = $this->getSettingsValue(PluginConfigurationValueNames::AFTER_CHECKOUT_PAYMENT_STATUS);

        return new AfterCheckoutPaymentStatus($value);
    }

    /**
     * @return string
     */
    public function getInvoiceDocumentKey()
    {
        $invoiceDocumentKey = $this->getSettingsValue(PluginConfigurationValueNames::INVOICE_DOCUMENT_KEY);
        if (is_string($invoiceDocumentKey) && strlen($invoiceDocumentKey) > 0) {
            return $invoiceDocumentKey;
        }

        return 'invoice';
    }

    /**
     * @return string|null
     */
    public function getCustomErrorMessage()
    {
        $errorMessage = $this->getSettingsValue(PluginConfigurationValueNames::ERROR_MESSAGE);
        if ('' === $errorMessage) {
            return null;
        }

        return $errorMessage;
    }

    /**
     * @param string $settingName
     *
     * @return string
     */
    private function getSettingsValue($settingName)
    {
        $settingName = (string) $settingName;
        /** @var DBALConfigReader */
        $configReader = Shopware()->Container()->get('shopware.plugin.config_reader');
        $config = $configReader->getByPluginName(PluginConfigurationValueNames::PLUGIN_NAME);

        return $config[$settingName];
    }
}
