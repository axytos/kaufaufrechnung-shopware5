<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common;

use AxytosKaufAufRechnungShopware5\Adapter\Common\ShippingBasketPosition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use PHPUnit\Framework\Attributes\Before;

class ShippingBasketPositionTest extends TestCase
{
    /**
     * @var Order&MockObject
     */
    private $order;

    /**
     * @var ShippingBasketPosition
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

        $this->sut = new ShippingBasketPosition(
            $this->order
        );
    }

    /**
     * @return void
     */
    public function test_getProductNumber_returnsCorrectValue()
    {
        $result = $this->sut->getProductNumber();

        $this->assertEquals('0', $result);
    }

    /**
     * @return void
     */
    public function test_getProductName_returnsCorrectValue()
    {
        $result = $this->sut->getProductName();

        $this->assertEquals('Shipping', $result);
    }

    /**
     * @return void
     */
    public function test_getProductCategory_returnsCorrectValue()
    {
        $result = $this->sut->getProductCategory();

        $this->assertNull($result);
    }

    /**
     * @return void
     */
    public function test_getQuantity_returnsCorrectValue()
    {
        $result = $this->sut->getQuantity();

        $this->assertEquals(1, $result);
    }

    /**
     * @return void
     */
    public function test_getTaxPercent_returnsCorrectValue()
    {
        $this->order
            ->method('getInvoiceShippingTaxRate')
            ->willReturn(19.0);

        $result = $this->sut->getTaxPercent();

        $this->assertEquals(19.0, $result);
    }

    /**
     * @return void
     */
    public function test_getNetPricePerUnit_returnsCorrectValue()
    {
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(10.0);

        $result = $this->sut->getNetPricePerUnit();

        $this->assertEquals(10.0, $result);
    }

    /**
     * @return void
     */
    public function test_getGrossPricePerUnit_returnsCorrectValue()
    {
        $this->order
            ->method('getInvoiceShipping')
            ->willReturn(11.9);

        $result = $this->sut->getGrossPricePerUnit();

        $this->assertEquals(11.9, $result);
    }

    /**
     * @return void
     */
    public function test_getNetPositionTotal_returnsCorrectValue()
    {
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(10.0);

        $result = $this->sut->getNetPositionTotal();

        $this->assertEquals(10.0, $result);
    }

    /**
     * @return void
     */
    public function test_getGrossPositionTotal_returnsCorrectValue()
    {
        $this->order
            ->method('getInvoiceShipping')
            ->willReturn(11.9);

        $result = $this->sut->getGrossPositionTotal();

        $this->assertEquals(11.9, $result);
    }
}
