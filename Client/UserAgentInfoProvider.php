<?php

namespace AxytosKaufAufRechnungShopware5\Client;

use Axytos\ECommerce\Abstractions\UserAgentInfoProviderInterface;
use Axytos\ECommerce\PackageInfo\ComposerPackageInfoProvider;

class UserAgentInfoProvider implements UserAgentInfoProviderInterface
{
    /**
     * @var \Axytos\ECommerce\PackageInfo\ComposerPackageInfoProvider
     */
    private $composerPackageInfoProvider;

    public function __construct(ComposerPackageInfoProvider $composerPackageInfoProvider)
    {
        $this->composerPackageInfoProvider = $composerPackageInfoProvider;
    }

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
        $packageName = 'axytos/kaufaufrechnung-shopware5';

        /** @phpstan-ignore-next-line */
        return $this->composerPackageInfoProvider->getVersion($packageName);
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
        $packageName = 'shopware/shopware';

        /** @phpstan-ignore-next-line */
        return $this->composerPackageInfoProvider->getVersion($packageName);
    }
}
