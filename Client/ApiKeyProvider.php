<?php

namespace AxytosKaufAufRechnungShopware5\Client;

use Axytos\ECommerce\Abstractions\ApiKeyProviderInterface;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;

class ApiKeyProvider implements ApiKeyProviderInterface
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration
     */
    public $pluginConfig;

    public function __construct(PluginConfiguration $pluginConfig)
    {
        $this->pluginConfig = $pluginConfig;
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->pluginConfig->getApiKey();
    }
}
