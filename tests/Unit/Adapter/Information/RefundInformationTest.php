<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information;

use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\Basket;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Information\RefundInformation;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Document\Document;

class RefundInformationTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order&MockObject
     */
    private $order;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketFactory&MockObject
     */
    private $basketFactory;

    /**
     * @var RefundInformation
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    #[Before]
    public function beforeEach()
    {
        $this->order = $this->createMock(Order::class);
        $this->basketFactory = $this->createMock(BasketFactory::class);

        $this->sut = new RefundInformation(
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
    public function test_getBasket_returns_basket_for_credit_document_if_credit_document_is_found()
    {
        $expected = $this->createMock(Basket::class);

        /** @var Document&MockObject */
        $creditDocument = $this->createMock(Document::class);
        $creditDocument->method('getOrder')->willReturn($this->createMock(Order::class));

        $this->order->method('findCreditDocument')->willReturn($creditDocument);

        $this->basketFactory->method('create')->with($creditDocument->getOrder())->willReturn($expected);

        $basket = $this->sut->getBasket();

        $this->assertSame($expected, $basket);
    }

    /**
     * @return void
     */
    public function test_getBasket_returns_basket_for_order_if_credit_document_is_not_found()
    {
        $expected = $this->createMock(Basket::class);

        $this->order->method('findCreditDocument')->willReturn(null);

        $this->basketFactory->method('create')->with($this->order)->willReturn($expected);

        $basket = $this->sut->getBasket();

        $this->assertSame($expected, $basket);
    }
}
