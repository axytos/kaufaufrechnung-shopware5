<?php

namespace AxytosKaufAufRechnungShopware5\Logging;

use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use Shopware\Components\Logger;

class LoggerAdapter implements LoggerAdapterInterface
{
    /**
     * @var \Shopware\Components\Logger
     */
    private $pluginLogger;

    public function __construct()
    {
        $hasPluginSpecificLogger = Shopware()->Container()->has('axytos_kauf_auf_rechnung_shopware5.logger');
        if ($hasPluginSpecificLogger) {
            /** @phpstan-ignore-next-line */
            $this->pluginLogger = Shopware()->Container()->get('axytos_kauf_auf_rechnung_shopware5.logger');
        } else {
            /** @phpstan-ignore-next-line */
            $this->pluginLogger = Shopware()->Container()->get('pluginlogger');
        }
    }

    /**
     * @param string $message
     * @return void
     */
    public function error($message)
    {
        $this->pluginLogger->error($message);
    }

    /**
     * @param string $message
     * @return void
     */
    public function warning($message)
    {
        $this->pluginLogger->warning($message);
    }

    /**
     * @param string $message
     * @return void
     */
    public function info($message)
    {
        $this->pluginLogger->info($message);
    }

    /**
     * @param string $message
     * @return void
     */
    public function debug($message)
    {
        $this->pluginLogger->debug($message);
    }
}
