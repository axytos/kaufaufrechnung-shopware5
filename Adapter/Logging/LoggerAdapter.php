<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Logging;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Logging\LoggerAdapterInterface;

class LoggerAdapter implements LoggerAdapterInterface
{
    /**
     * @return \Shopware\Components\Logger
     */
    private static function getShopwareLogger()
    {
        $container = Shopware()->Container();

        // For Shopware >= 5.6

        if ($container->has('axytos_kauf_auf_rechnung_shopware5.logger')) {
            /** @phpstan-ignore-next-line */
            return $container->get('axytos_kauf_auf_rechnung_shopware5.logger');
        }

        // For Shopware < 5.6

        /** @phpstan-ignore-next-line */
        return $container->get('pluginlogger');
    }

    /**
     * @var \Shopware\Components\Logger
     */
    private $pluginLogger;

    public function __construct()
    {
        $this->pluginLogger = self::getShopwareLogger();
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
