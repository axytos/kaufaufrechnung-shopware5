<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Logging;

use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use Shopware\Components\Logger;

class LoggerAdapter implements LoggerAdapterInterface
{
    private Logger $pluginLogger;

    public function __construct(Logger $pluginLogger)
    {
        $this->pluginLogger = $pluginLogger;
    }

    public function error(string $message): void
    {
        $this->pluginLogger->error($message);
    }

    public function warning(string $message): void
    {
        $this->pluginLogger->warning($message);
    }

    public function info(string $message): void
    {
        $this->pluginLogger->info($message);
    }

    public function debug(string $message): void
    {
        $this->pluginLogger->debug($message);
    }
}
