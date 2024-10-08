<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Client;

use Axytos\ECommerce\Abstractions\UserAgentInfoProviderInterface;
use AxytosKaufAufRechnungShopware5\Client\UserAgentInfoProvider;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class UserAgentInfoProviderTest extends TestCase
{
    /**
     * @var UserAgentInfoProvider
     */
    private $sut;

    /**
     * @return void
     *
     * @before
     */
    #[Before]
    public function beforeEach()
    {
        $this->sut = new UserAgentInfoProvider();
    }

    /**
     * @return void
     */
    public function test_implements_user_agent_info_provider_interface()
    {
        $this->assertInstanceOf(UserAgentInfoProviderInterface::class, $this->sut);
    }

    /**
     * @return void
     */
    public function test_get_plugin_name_returns_kauf_auf_rechnung()
    {
        $pluginName = $this->sut->getPluginName();

        $this->assertEquals('KaufAufRechnung', $pluginName);
    }

    /**
     * @return void
     */
    public function test_get_plugin_version_returns_version_from_composer()
    {
        $expected = $this->getComposerPackageVersion();

        $actual = $this->sut->getPluginVersion();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return void
     */
    public function test_get_shop_system_name_returns_shopware()
    {
        $shopSystemName = $this->sut->getShopSystemName();

        $this->assertEquals('Shopware', $shopSystemName);
    }

    /**
     * @return void
     */
    public function test_get_shop_system_version_returns_version_from_composer()
    {
        $expected = '5.X.X';

        $actual = $this->sut->getShopSystemVersion();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return string
     */
    private function getComposerPackageVersion()
    {
        /** @var string */
        $composerJson = file_get_contents(__DIR__ . '/../../../composer.json');
        /** @var string[] */
        $config = json_decode($composerJson, true);

        return $config['version'];
    }
}
