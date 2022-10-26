<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Client;

use Axytos\ECommerce\Abstractions\ApiKeyProviderInterface;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;

class ApiKeyProvider implements ApiKeyProviderInterface
{
    public PluginConfiguration $pluginConfig;

    public function __construct(PluginConfiguration $pluginConfig)
    {
        $this->pluginConfig = $pluginConfig;
    }

    public function getApiKey(): string
    {
        return $this->pluginConfig->getApiKey();
    }
}
