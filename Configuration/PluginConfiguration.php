<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Configuration;

use Shopware\Components\Plugin\Configuration\ReaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
