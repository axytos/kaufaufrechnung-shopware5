<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information;

use AxytosKaufAufRechnungShopware5\Adapter\Common\Basket;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\InvoiceInformation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Document\Document;

class InvoiceInformationTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order&MockObject
     */
    private $order;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory&MockObject
     */
    private $basketFactory;

    /**
     * @var InvoiceInformation
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->order = $this->createMock(Order::class);
        $this->basketFactory = $this->createMock(BasketFactory::class);

        $this->sut = new InvoiceInformation(
            $this->order,
            $this->basketFactory
        );
    }

    /**
     * @return void
     */
    public function test_getOrderNumber_returnsOrderNumber()
    {
        $this->order
            ->method('getNumber')
            ->willReturn('order-123');

        $this->assertEquals('order-123', $this->sut->getOrderNumber());
    }

    /**
     * @return void
     */
    public function test_getInvoiceNumber_returns_invoice_number_when_invoice_document_is_not_found()
    {
        /** @var Document&MockObject */
        $invoiceDocument = $this->createMock(Document::class);
        $invoiceDocument->method('getDocumentId')->willReturn('InvoiceNumber1234');

        $this->order->method('findInvoiceDocument')->willReturn($invoiceDocument);

        $invoiceNumber = $this->sut->getInvoiceNumber();

        $this->assertEquals('InvoiceNumber1234', $invoiceNumber);
    }

    /**
     * @return void
     */
    public function test_getInvoiceNumber_returns_empty_string_when_invoice_document_is_not_found()
    {
        $this->order->method('findInvoiceDocument')->willReturn(null);

        $invoiceNumber = $this->sut->getInvoiceNumber();

        $this->assertTrue(is_string($invoiceNumber));
        $this->assertTrue($invoiceNumber === '');
    }

    /**
     * @return void
     */
    public function test_getBasket_returns_basket()
    {
        $expected = $this->createMock(Basket::class);

        $this->basketFactory->method('create')->with($this->order)->willReturn($expected);

        $basket = $this->sut->getBasket();

        $this->assertSame($expected, $basket);
    }
}
