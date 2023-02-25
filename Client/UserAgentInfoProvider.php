<?php

namespace AxytosKaufAufRechnungShopware5\Client;

use Axytos\ECommerce\Abstractions\UserAgentInfoProviderInterface;

class UserAgentInfoProvider implements UserAgentInfoProviderInterface
{
    /**
     * @return string
     */
    public function getPluginName()
    {
        return "KaufAufRechnung";
    }

    /**
     * @return string
     */
    public function getPluginVersion()
    {
        return '1.3.0';
    }

    /**
     * @return string
     */
    public function getShopSystemName()
    {
        return "Shopware";
    }

    /**
     * @return string
     */
    public function getShopSystemVersion()
    {
        try {
            $config = Shopware()->Config();
            /** @phpstan-ignore-next-line */
            $version = $config->Version;
            return $version;
        } catch (\Throwable $th) {
            return '5.X.X';
        } catch (\Exception $th) { /** @phpstan-ignore-line because php5 compatibility */
            return '5.X.X';
        }
    }
}
