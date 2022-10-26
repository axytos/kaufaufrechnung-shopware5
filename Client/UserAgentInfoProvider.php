<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Client;

use Axytos\ECommerce\Abstractions\UserAgentInfoProviderInterface;
use Axytos\ECommerce\PackageInfo\ComposerPackageInfoProvider;

class UserAgentInfoProvider implements UserAgentInfoProviderInterface
{
    private ComposerPackageInfoProvider $composerPackageInfoProvider;

    public function __construct(ComposerPackageInfoProvider $composerPackageInfoProvider)
    {
        $this->composerPackageInfoProvider = $composerPackageInfoProvider;
    }

    public function getPluginName(): string
    {
        return "KaufAufRechnung";
    }

    public function getPluginVersion(): string
    {
        $packageName = 'axytos/kaufaufrechnung-shopware5';

        /** @phpstan-ignore-next-line */
        return $this->composerPackageInfoProvider->getVersion($packageName);
    }

    public function getShopSystemName(): string
    {
        return "Shopware";
    }

    public function getShopSystemVersion(): string
    {
        $packageName = 'shopware/shopware';

        /** @phpstan-ignore-next-line */
        return $this->composerPackageInfoProvider->getVersion($packageName);
    }
}
