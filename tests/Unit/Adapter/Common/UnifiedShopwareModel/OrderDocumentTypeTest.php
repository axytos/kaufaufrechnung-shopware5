<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common\UnifiedShopwareModel;

use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderDocumentType;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\ShopwareModelReflector;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Shopware\Models\Document\Document as ShopwareDocumentType;

class OrderDocumentTypeTest extends TestCase
{
    /**
     * @var ShopwareDocumentType&MockObject
     */
    private $shopwareDocumentType;

    /**
     * @var ShopwareModelReflector&MockObject
     */
    private $shopwareModelReflector;

    /**
     * @var PluginConfiguration&MockObject
     */
    private $pluginConfiguration;

    /**
     * @var OrderDocumentType
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->shopwareDocumentType = $this->createMock(ShopwareDocumentType::class);
        $this->shopwareModelReflector = $this->createMock(ShopwareModelReflector::class);
        $this->pluginConfiguration = $this->createMock(PluginConfiguration::class);

        $this->sut = new OrderDocumentType(
            $this->shopwareDocumentType,
            $this->shopwareModelReflector,
            $this->pluginConfiguration
        );
    }

    /**
     * @return void
     */
    public function test_getKey_returns_key_if_method_getKey_exists()
    {
        $this->shopwareModelReflector->method('hasMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn(true);
        $this->shopwareModelReflector->method('callMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn('DocumentTypeKey');

        $key = $this->sut->getKey();

        $this->assertEquals('DocumentTypeKey', $key);
    }

    /**
     * @dataProvider key_inference_test_cases
     * @param string $name
     * @param string|null $expectedKey
     * @param string $invoiceKey
     * @return void
     */
    public function test_getKey_infers_key_from_name_if_method_getKey_does_not_exist($name, $expectedKey, $invoiceKey)
    {
        $this->shopwareDocumentType->method('getName')->willReturn($name);
        $this->shopwareModelReflector->method('hasMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn(false);
        $this->pluginConfiguration->method('getInvoiceDocumentKey')->willReturn($invoiceKey);

        $key = $this->sut->getKey();

        $this->assertEquals($expectedKey, $key);
        $this->assertEquals($expectedKey === $invoiceKey, $this->sut->isInvoiceDocument());
        $this->assertEquals($expectedKey === 'credit', $this->sut->isCreditDocument());
        $this->assertEquals($expectedKey === 'delivery_note', $this->sut->isDeliveryNoteDocument());
        $this->assertEquals($expectedKey === 'cancellation', $this->sut->isCancellationDocument());
    }

    /**
     * @return array<array<mixed>>
     */
    public function key_inference_test_cases()
    {
        return [
            ['invoice', 'invoice', 'invoice'],
            ['rechnung', 'invoice', 'invoice'],
            ['Rechnung', 'invoice', 'invoice'],
            ['credit', 'credit', 'invoice'],
            ['gutschrift', 'credit', 'invoice'],
            ['delivery', 'delivery_note', 'invoice'],
            ['lieferschein', 'delivery_note', 'invoice'],
            ['StornoRechnung', 'cancellation', 'invoice'],
            ['cancellation', 'cancellation', 'invoice'],
            ['foobar', null, 'invoice'],
            ['invoice', 'special', 'special'],
            ['rechnung', 'special', 'special'],
            ['Rechnung', 'special', 'special'],
        ];
    }
}
