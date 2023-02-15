<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Client;

use Axytos\ECommerce\Abstractions\UserAgentInfoProviderInterface;
use AxytosKaufAufRechnungShopware5\Client\UserAgentInfoProvider;
use Axytos\ECommerce\PackageInfo\ComposerPackageInfoProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserAgentInfoProviderTest extends TestCase
{
    /** @var ComposerPackageInfoProvider&MockObject $composerPackageInfoProvider */
    private $composerPackageInfoProvider;
    /**
     * @var \AxytosKaufAufRechnungShopware5\Client\UserAgentInfoProvider
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->composerPackageInfoProvider = $this->createMock(ComposerPackageInfoProvider::class);
        $this->sut = new UserAgentInfoProvider($this->composerPackageInfoProvider);
    }

    /**
     * @return void
     */
    public function test_implements_UserAgentInfoProviderInterface()
    {
        $this->assertInstanceOf(UserAgentInfoProviderInterface::class, $this->sut);
    }

    /**
     * @return void
     */
    public function test_getPluginName_returns_KaufAufRechnung()
    {
        $pluginName = $this->sut->getPluginName();

        $this->assertEquals("KaufAufRechnung", $pluginName);
    }

    /**
     * @return string
     */
    public function getComposerPackageName()
    {
        /** @var string */
        $composerJson = file_get_contents(__DIR__ . '/../../../composer.json');
        /** @var string[] */
        $config = json_decode($composerJson, true);

        return $config["name"];
    }

    /**
     * @return void
     */
    public function test_getPluginVersion_returns_version_from_composer()
    {
        $expected = "version";

        $packageName = $this->getComposerPackageName();

        $this->composerPackageInfoProvider
            ->method('getVersion')
            ->with($packageName)
            ->willReturn($expected);

        $actual = $this->sut->getPluginVersion();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_getShopSystemName_returns_Shopware()
    {
        $shopSystemName = $this->sut->getShopSystemName();

        $this->assertEquals("Shopware", $shopSystemName);
    }

    /**
     * @return void
     */
    public function test_getShopSystemVersion_returns_version_from_composer()
    {
        $expected = "version";
        $this->composerPackageInfoProvider
            ->method('getVersion')
            ->with("shopware/shopware")
            ->willReturn($expected);

        $actual = $this->sut->getShopSystemVersion();

        $this->assertEquals($expected, $actual);
    }
}
