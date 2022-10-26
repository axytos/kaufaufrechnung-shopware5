<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Client;

use Axytos\ECommerce\Abstractions\ApiHostProviderInterface;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;

class ApiHostProvider implements ApiHostProviderInterface
{
    public PluginConfiguration $pluginConfig;

    public function __construct(PluginConfiguration $pluginConfig)
    {
        $this->pluginConfig = $pluginConfig;
    }

    public function getApiHost(): string
    {
        return $this->pluginConfig->getApiHost();
    }
}
