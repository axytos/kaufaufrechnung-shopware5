<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common\UnifiedShopwareModel;

use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\ShopwareModelReflector;
use PHPUnit\Framework\TestCase;
use stdClass;
use Shopware\Models\Document\Document as ShopwareDocumentType;

class ShopwareModelReflectorTest extends TestCase
{
    /**
     * @return void
     */
    public function test_hasMethod_returns_true_if_method_exists()
    {
        $sut = new ShopwareModelReflector();
        $this->assertTrue($sut->hasMethod($this, 'test_hasMethod_returns_true_if_method_exists'));
    }

    /**
     * @return void
     */
    public function test_hasMethod_returns_false_if_method_does_not_exist()
    {
        $sut = new ShopwareModelReflector();
        $this->assertFalse($sut->hasMethod(new stdClass(), 'RandomMethodName'));
    }

    /**
     * @return void
     */
    public function test_callMethod_calls_method_on_shopware_model_instance()
    {
        $sut = new ShopwareModelReflector();

        $shopwareDocumentType = new ShopwareDocumentType();

        $sut->callMethod($shopwareDocumentType, 'setName', 'CustomDocumentName');

        $this->assertEquals('CustomDocumentName', $shopwareDocumentType->getName());
        $this->assertEquals('CustomDocumentName', $sut->callMethod($shopwareDocumentType, 'getName'));
    }

    /**
     * @return void
     */
    public function test_callMethod_throws_exepction_if_method_does_not_exist()
    {
        $this->expectException(\Exception::class);

        $sut = new ShopwareModelReflector();

        $sut->callMethod(new stdClass(), 'RandomMethodName');
    }
}
