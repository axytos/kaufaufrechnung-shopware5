<?php

namespace AxytosKaufAufRechnungShopware5\Client;

use Axytos\ECommerce\Abstractions\ApiHostProviderInterface;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;

class ApiHostProvider implements ApiHostProviderInterface
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
    public function getApiHost()
    {
        return $this->pluginConfig->getApiHost();
    }
}
