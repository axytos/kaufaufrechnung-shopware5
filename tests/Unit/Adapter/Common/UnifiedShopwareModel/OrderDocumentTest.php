<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common\UnifiedShopwareModel;

use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderDocument;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\ShopwareModelReflector;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository as OldOrderRepository;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Document\Document as ShopwareDocumentType;
use Shopware\Models\Order\Document\Document as ShopwareOrderDocument;
use Shopware\Models\Order\Order as ShopwareOrder;

/**
 * @internal
 */
class OrderDocumentTest extends TestCase
{
    /**
     * @var ShopwareDocumentType&MockObject
     */
    private $shopwareDocumentType;

    /**
     * @var ShopwareOrderDocument&MockObject
     */
    private $shopwareOrderDocument;

    /**
     * @var ShopwareModelReflector&MockObject
     */
    private $shopwareModelReflector;

    /**
     * @var PluginConfiguration&MockObject
     */
    private $pluginConfiguration;

    /**
     * @var OrderDocument
     */
    private $sut;

    /**
     * @before
     *
     * @return void
     */
    #[Before]
    public function beforeEach()
    {
        $this->shopwareDocumentType = $this->createMock(ShopwareDocumentType::class);

        $this->shopwareOrderDocument = $this->createMock(ShopwareOrderDocument::class);
        $this->shopwareOrderDocument->method('getType')->willReturn($this->shopwareDocumentType);

        $this->shopwareModelReflector = $this->createMock(ShopwareModelReflector::class);

        $this->pluginConfiguration = $this->createMock(PluginConfiguration::class);

        $this->sut = new OrderDocument(
            $this->shopwareOrderDocument,
            $this->createMock(OldOrderRepository::class),
            $this->shopwareModelReflector,
            $this->pluginConfiguration
        );
    }

    /**
     * @return void
     */
    public function test_get_document_id()
    {
        $this->shopwareOrderDocument->method('getDocumentId')->willReturn(1234);

        $documentId = $this->sut->getDocumentId();

        $this->assertEquals('1234', $documentId);
    }

    /**
     * @return void
     */
    public function test_get_order()
    {
        $shopwareOrder = $this->createMock(ShopwareOrder::class);

        $this->shopwareOrderDocument
            ->method('getOrder')
            ->willReturn($shopwareOrder)
        ;

        $this->assertInstanceOf(Order::class, $this->sut->getOrder());
    }

    /**
     * @dataProvider isInvoiceDocument_test_cases
     *
     * @param bool        $hasMethodGetKey
     * @param string|null $documentKey
     * @param string|null $configurationKey
     * @param string      $name
     * @param bool        $expected
     *
     * @return void
     */
    #[DataProvider('isInvoiceDocument_test_cases')]
    public function test_is_invoice_document($hasMethodGetKey, $documentKey, $configurationKey, $name, $expected)
    {
        $this->pluginConfiguration->method('getInvoiceDocumentKey')->willReturn($configurationKey);
        $this->shopwareModelReflector->method('hasMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn($hasMethodGetKey);
        $this->shopwareModelReflector->method('callMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn($documentKey);
        $this->shopwareDocumentType->method('getName')->willReturn($name);

        $this->assertEquals($expected, $this->sut->isInvoiceDocument());
    }

    /**
     * @return array<array<mixed>>
     */
    public static function isInvoiceDocument_test_cases()
    {
        return [
            [false, 'invoice', 'invoice', 'Rechnung', true],
            [false, 'invoice', 'invoice', 'unknown', false],
            [false, 'invoice', 'special', 'Rechnung', true],
            [false, 'invoice', 'special', 'unknown', false],

            [true, 'invoice', 'invoice', 'Rechnung', true],
            [true, 'invoice', 'invoice', 'unknown', true],
            [true, 'unknown', 'invoice', 'Rechnung', false],
            [true, 'invoice', 'special', 'Rechnung', false],
            [true, 'special', 'special', 'Rechnung', true],
        ];
    }

    /**
     * @dataProvider isCreditDocument_test_cases
     *
     * @param bool        $hasMethodGetKey
     * @param string|null $key
     * @param string      $name
     * @param bool        $expected
     *
     * @return void
     */
    #[DataProvider('isCreditDocument_test_cases')]
    public function test_is_credit_document($hasMethodGetKey, $key, $name, $expected)
    {
        $this->shopwareModelReflector->method('hasMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn($hasMethodGetKey);
        $this->shopwareModelReflector->method('callMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn($key);
        $this->shopwareDocumentType->method('getName')->willReturn($name);

        $this->assertEquals($expected, $this->sut->isCreditDocument());
    }

    /**
     * @return array<array<mixed>>
     */
    public static function isCreditDocument_test_cases()
    {
        return [
            [false, 'credit', 'Gutschrift', true],
            [false, 'credit', 'unknown', false],

            [true, 'credit', 'Gutschrift', true],
            [true, 'credit', 'unknown', true],
            [true, 'unknown', 'unknown', false],
        ];
    }

    /**
     * @dataProvider isDeliveryNoteDocument_test_cases
     *
     * @param bool        $hasMethodGetKey
     * @param string|null $key
     * @param string      $name
     * @param bool        $expected
     *
     * @return void
     */
    #[DataProvider('isDeliveryNoteDocument_test_cases')]
    public function test_is_delivery_note_document($hasMethodGetKey, $key, $name, $expected)
    {
        $this->shopwareModelReflector->method('hasMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn($hasMethodGetKey);
        $this->shopwareModelReflector->method('callMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn($key);
        $this->shopwareDocumentType->method('getName')->willReturn($name);

        $this->assertEquals($expected, $this->sut->isDeliveryNoteDocument());
    }

    /**
     * @return array<array<mixed>>
     */
    public static function isDeliveryNoteDocument_test_cases()
    {
        return [
            [false, 'delivery_note', 'Lieferschein', true],
            [false, 'delivery_note', 'unknown', false],

            [true, 'delivery_note', 'Lieferschein', true],
            [true, 'delivery_note', 'unknown', true],
            [true, 'unknown', 'unknown', false],
        ];
    }

    /**
     * @dataProvider isCancellationDocument_test_cases
     *
     * @param bool        $hasMethodGetKey
     * @param string|null $key
     * @param string      $name
     * @param bool        $expected
     *
     * @return void
     */
    #[DataProvider('isCancellationDocument_test_cases')]
    public function test_is_cancellation_document($hasMethodGetKey, $key, $name, $expected)
    {
        $this->shopwareModelReflector->method('hasMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn($hasMethodGetKey);
        $this->shopwareModelReflector->method('callMethod')->with($this->shopwareDocumentType, 'getKey')->willReturn($key);
        $this->shopwareDocumentType->method('getName')->willReturn($name);

        $this->assertEquals($expected, $this->sut->isCancellationDocument());
    }

    /**
     * @return array<array<mixed>>
     */
    public static function isCancellationDocument_test_cases()
    {
        return [
            [false, 'cancellation', 'Stornorechnung', true],
            [false, 'cancellation', 'unknown', false],

            [true, 'cancellation', 'Stornorechnung', true],
            [true, 'cancellation', 'unknown', true],
            [true, 'unknown', 'unknown', false],
        ];
    }
}
