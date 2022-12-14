<?php

namespace AxytosKaufAufRechnungShopware5\Configuration;

use Shopware\Components\Plugin\Configuration\ReaderInterface;

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
     * @return \AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutOrderStatus
     */
    public function getAfterCheckoutOrderStatus()
    {
        $value = $this->getSettingsValue(PluginConfigurationValueNames::AFTER_CHECKOUT_ORDER_STATUS);

        return new AfterCheckoutOrderStatus($value);
    }

    /**
     * @return \AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutPaymentStatus
     */
    public function getAfterCheckoutPaymentStatus()
    {
        $value = $this->getSettingsValue(PluginConfigurationValueNames::AFTER_CHECKOUT_PAYMENT_STATUS);

        return new AfterCheckoutPaymentStatus($value);
    }

    /**
     * @return string
     * @param string $settingName
     */
    private function getSettingsValue($settingName)
    {
        $settingName = (string) $settingName;
        /** @var ReaderInterface */
        $configReader = Shopware()->Container()->get('shopware.plugin.config_reader');
        $config = $configReader->getByPluginName(PluginConfigurationValueNames::PLUGIN_NAME);
        return $config[$settingName];
    }
}
