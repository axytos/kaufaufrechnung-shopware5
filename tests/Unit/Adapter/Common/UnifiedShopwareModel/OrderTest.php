<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common\UnifiedShopwareModel;

use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderAttributes;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderDocument;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\ShopwareModelReflector;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Shopware\Models\Document\Document as ShopwareDocumentType;
use Shopware\Models\Order\Order as ShopwareOrder;
use Shopware\Models\Order\Document\Document as ShopwareOrderDocument;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository as OldOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\DataProvider;

class OrderTest extends TestCase
{
    /**
     * @var ShopwareOrder&MockObject
     */
    private $shopwareOrder;

    /**
     * @var OldOrderRepository&MockObject
     */
    private $orderRepository;

    /**
     * @var ShopwareModelReflector&MockObject
     */
    private $shopwareModelReflector;

    /**
     * @var PluginConfiguration&MockObject
     */
    private $pluginConfiguration;

    /**
     * @var Order
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    #[Before]
    public function beforeEach()
    {
        $this->shopwareOrder = $this->createMock(ShopwareOrder::class);
        $this->orderRepository = $this->createMock(OldOrderRepository::class);
        $this->shopwareModelReflector = $this->createMock(ShopwareModelReflector::class);
        $this->pluginConfiguration = $this->createMock(PluginConfiguration::class);
        $this->pluginConfiguration->method('getInvoiceDocumentKey')->willReturn('invoice');

        $this->sut = new Order(
            $this->shopwareOrder,
            $this->orderRepository,
            $this->shopwareModelReflector,
            $this->pluginConfiguration
        );
    }

    /**
     * @return void
     */
    public function test_persist_saves_order()
    {
        $this->orderRepository->expects($this->once())->method('saveOrder')->with($this->shopwareOrder);

        $this->sut->persist();
    }

    /**
     * @return void
     */
    public function test_getAttributes_returns_instance_of_OrderAttributes()
    {
        $this->assertInstanceOf(OrderAttributes::class, $this->sut->getAttributes());
    }

    /**
     * @dataProvider getter_delegation_test_cases
     * @param string $getterName
     * @param mixed $expectedResult
     * @return void
     */
    #[DataProvider('getter_delegation_test_cases')]
    public function test_Order_delegates_getter_calls_to_original_instance($getterName, $expectedResult)
    {
        $this->shopwareOrder->method($getterName)->willReturn($expectedResult);

        /** @var callable */
        $callable = [$this->sut, $getterName];
        $actualResult = call_user_func($callable);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array<array<mixed>>
     */
    public static function getter_delegation_test_cases()
    {
        return [
            ['getCurrency', 'EUR'],
            ['getInvoiceAmount', 42.42],
            ['getInvoiceAmountNet', 42.42],
            ['getInvoiceShipping', 42.42],
            ['getInvoiceShippingNet', 42.42],
            ['getTemporaryId', 'id-2312'],
            ['getNumber', '123456'],
            ['getNumber', null],
            ['getTrackingCode', '55555'],
            ['getDetails', new \Doctrine\Common\Collections\ArrayCollection()],
            ['getDispatch', new \Shopware\Models\Dispatch\Dispatch()],
            ['getCustomer', new \Shopware\Models\Customer\Customer()],
            ['getBilling', new \Shopware\Models\Order\Billing()],
            ['getShipping', new \Shopware\Models\Order\Shipping()],
        ];
    }

    /**
     * @return void
     */
    public function test_getInvoiceShippingTaxRate_return_original_value_if_method_getInvoiceShippingTaxRate_exists()
    {
        $this->shopwareModelReflector->method('hasMethod')->with($this->shopwareOrder, 'getInvoiceShippingTaxRate')->willReturn(true);
        $this->shopwareModelReflector->method('callMethod')->with($this->shopwareOrder, 'getInvoiceShippingTaxRate')->willReturn(19);

        $this->assertEquals(19, $this->sut->getInvoiceShippingTaxRate());
    }

    /**
     * @dataProvider calculate_getInvoiceShippingTaxRate_test_cases
     * @param float $invoiceShipping
     * @param float $invoiceShippingNet
     * @param float $expectedInvoiceShippingTaxRate
     * @return void
     */
    #[DataProvider('calculate_getInvoiceShippingTaxRate_test_cases')]
    public function test_getInvoiceShippingTaxRate_calculates_value_if_method_getInvoiceShippingTaxRate_does_not_exist(
        $invoiceShipping,
        $invoiceShippingNet,
        $expectedInvoiceShippingTaxRate
    ) {
        $this->shopwareModelReflector->method('hasMethod')->with($this->shopwareOrder, 'getInvoiceShippingTaxRate')->willReturn(false);

        $this->shopwareOrder->method('getInvoiceShipping')->willReturn($invoiceShipping);
        $this->shopwareOrder->method('getInvoiceShippingNet')->willReturn($invoiceShippingNet);

        $this->assertEquals($expectedInvoiceShippingTaxRate, $this->sut->getInvoiceShippingTaxRate());
    }

    /**
     * @return array<array<mixed>>
     */
    public static function calculate_getInvoiceShippingTaxRate_test_cases()
    {
        return [
            [100, 81, 19],
            [100, 0, 100],
            [0, 81, 0]
        ];
    }

    /**
     * @return void
     */
    public function test_savePaymentStatus_savesPaymentStatusWithOrderRepository()
    {
        $testPaymentStatusId = 123;
        $this->orderRepository
            ->expects($this->once())
            ->method('savePaymentStatus')
            ->with($this->shopwareOrder, $testPaymentStatusId);

        $this->sut->savePaymentStatus($testPaymentStatusId);
    }

    /**
     * @dataProvider document_is_not_found_test_cases
     * @param ArrayCollection<int,ShopwareOrderDocument>|null $documents
     * @return void
     */
    #[DataProvider('document_is_not_found_test_cases')]
    public function test_findInvoiceDocument_returns_null_if_document_is_not_found($documents)
    {
        $this->shopwareOrder->method('getDocuments')->willReturn($documents);

        $this->assertNull($this->sut->findInvoiceDocument());
    }

    /**
     * @return void
     */
    public function test_findInvoiceDocument_returns_document_if_document_is_found_by_name()
    {
        /** @var ArrayCollection<int,ShopwareOrderDocument> */
        $documents = new ArrayCollection();

        /** @var ShopwareDocumentType&MockObject */
        $documentType = $this->createMock(ShopwareDocumentType::class);
        $documentType->method('getName')->willReturn('Rechnung');

        /** @var ShopwareOrderDocument&MockObject */
        $document = $this->createMock(ShopwareOrderDocument::class);
        $document->method('getDocumentId')->willReturn('123456');
        $document->method('getType')->willReturn($documentType);

        $documents->add($document);

        $this->shopwareOrder->method('getDocuments')->willReturn($documents);

        $actual = $this->sut->findInvoiceDocument();

        $this->assertInstanceOf(OrderDocument::class, $actual);
        $this->assertEquals('123456', $actual->getDocumentId());
    }

    /**
     * @dataProvider document_is_not_found_test_cases
     * @param ArrayCollection<int,ShopwareOrderDocument>|null $documents
     * @return void
     */
    #[DataProvider('document_is_not_found_test_cases')]
    public function test_findCancellationDocument_returns_null_if_document_is_not_found($documents)
    {
        $this->shopwareOrder->method('getDocuments')->willReturn($documents);

        $this->assertNull($this->sut->findCancellationDocument());
    }

    /**
     * @return void
     */
    public function test_findCancellationDocument_returns_document_if_document_is_found_by_name()
    {
        /** @var ArrayCollection<int,ShopwareOrderDocument> */
        $documents = new ArrayCollection();

        /** @var ShopwareDocumentType&MockObject */
        $documentType = $this->createMock(ShopwareDocumentType::class);
        $documentType->method('getName')->willReturn('Stornorechnung');

        /** @var ShopwareOrderDocument&MockObject */
        $document = $this->createMock(ShopwareOrderDocument::class);
        $document->method('getDocumentId')->willReturn('123456');
        $document->method('getType')->willReturn($documentType);

        $documents->add($document);

        $this->shopwareOrder->method('getDocuments')->willReturn($documents);

        $actual = $this->sut->findCancellationDocument();

        $this->assertInstanceOf(OrderDocument::class, $actual);
        $this->assertEquals('123456', $actual->getDocumentId());
    }

    /**
     * @dataProvider document_is_not_found_test_cases
     * @param ArrayCollection<int,ShopwareOrderDocument>|null $documents
     * @return void
     */
    #[DataProvider('document_is_not_found_test_cases')]
    public function test_findCreditDocument_returns_null_if_document_is_not_found($documents)
    {
        $this->shopwareOrder->method('getDocuments')->willReturn($documents);

        $this->assertNull($this->sut->findCreditDocument());
    }

    /**
     * @return void
     */
    public function test_findCreditDocument_returns_document_if_document_is_found_by_name()
    {
        /** @var ArrayCollection<int,ShopwareOrderDocument> */
        $documents = new ArrayCollection();

        /** @var ShopwareDocumentType&MockObject */
        $documentType = $this->createMock(ShopwareDocumentType::class);
        $documentType->method('getName')->willReturn('Gutschrift');

        /** @var ShopwareOrderDocument&MockObject */
        $document = $this->createMock(ShopwareOrderDocument::class);
        $document->method('getDocumentId')->willReturn('123456');
        $document->method('getType')->willReturn($documentType);

        $documents->add($document);

        $this->shopwareOrder->method('getDocuments')->willReturn($documents);

        $actual = $this->sut->findCreditDocument();

        $this->assertInstanceOf(OrderDocument::class, $actual);
        $this->assertEquals('123456', $actual->getDocumentId());
    }

    /**
     * @dataProvider document_is_not_found_test_cases
     * @param ArrayCollection<int,ShopwareOrderDocument>|null $documents
     * @return void
     */
    #[DataProvider('document_is_not_found_test_cases')]
    public function test_findDeliveryNoteDocument_returns_null_if_document_is_not_found($documents)
    {
        $this->shopwareOrder->method('getDocuments')->willReturn($documents);

        $this->assertNull($this->sut->findDeliveryNoteDocument());
    }

    /**
     * @return array<array<mixed>>
     */
    public static function document_is_not_found_test_cases()
    {
        return [
            [null],
            [new ArrayCollection()]
        ];
    }

    /**
     * @return void
     */
    public function test_findDeliveryNoteDocument_returns_document_if_document_is_found_by_name()
    {
        /** @var ArrayCollection<int,ShopwareOrderDocument> */
        $documents = new ArrayCollection();

        /** @var ShopwareDocumentType&MockObject */
        $documentType = $this->createMock(ShopwareDocumentType::class);
        $documentType->method('getName')->willReturn('Lieferschein');

        /** @var ShopwareOrderDocument&MockObject */
        $document = $this->createMock(ShopwareOrderDocument::class);
        $document->method('getDocumentId')->willReturn('123456');
        $document->method('getType')->willReturn($documentType);

        $documents->add($document);

        $this->shopwareOrder->method('getDocuments')->willReturn($documents);

        $actual = $this->sut->findDeliveryNoteDocument();

        $this->assertInstanceOf(OrderDocument::class, $actual);
        $this->assertEquals('123456', $actual->getDocumentId());
    }
}
