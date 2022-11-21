<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Configuration;

use Shopware\Components\Plugin\Configuration\ReaderInterface;

class PluginConfiguration
{
    public function getApiHost(): string
    {
        return strval($this->getSettingsValue(PluginConfigurationValueNames::API_HOST));
    }

    public function getApiKey(): string
    {
        return strval($this->getSettingsValue(PluginConfigurationValueNames::API_KEY));
    }

    public function getClientSecret(): ?string
    {
        return $this->getSettingsValue(PluginConfigurationValueNames::CLIENT_SECRET);
    }

    public function getAfterCheckoutOrderStatus(): AfterCheckoutOrderStatus
    {
        $value = $this->getSettingsValue(PluginConfigurationValueNames::AFTER_CHECKOUT_ORDER_STATUS);

        return new AfterCheckoutOrderStatus($value);
    }

    public function getAfterCheckoutPaymentStatus(): AfterCheckoutPaymentStatus
    {
        $value = $this->getSettingsValue(PluginConfigurationValueNames::AFTER_CHECKOUT_PAYMENT_STATUS);

        return new AfterCheckoutPaymentStatus($value);
    }

    /**
     * @return string
     */
    private function getSettingsValue(string $settingName)
    {
        /** @var ReaderInterface */
        $configReader = Shopware()->Container()->get('shopware.plugin.config_reader');
        $config = $configReader->getByPluginName(PluginConfigurationValueNames::PLUGIN_NAME);
        return $config[$settingName];
    }
}
