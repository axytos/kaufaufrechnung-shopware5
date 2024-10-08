<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common;

use AxytosKaufAufRechnungShopware5\Adapter\Common\ShippingBasketPosition;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
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
     *
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
    public function test_get_product_number_returns_correct_value()
    {
        $result = $this->sut->getProductNumber();

        $this->assertEquals('0', $result);
    }

    /**
     * @return void
     */
    public function test_get_product_name_returns_correct_value()
    {
        $result = $this->sut->getProductName();

        $this->assertEquals('Shipping', $result);
    }

    /**
     * @return void
     */
    public function test_get_product_category_returns_correct_value()
    {
        $result = $this->sut->getProductCategory();

        $this->assertNull($result);
    }

    /**
     * @return void
     */
    public function test_get_quantity_returns_correct_value()
    {
        $result = $this->sut->getQuantity();

        $this->assertEquals(1, $result);
    }

    /**
     * @return void
     */
    public function test_get_tax_percent_returns_correct_value()
    {
        $this->order
            ->method('getInvoiceShippingTaxRate')
            ->willReturn(19.0)
        ;

        $result = $this->sut->getTaxPercent();

        $this->assertEquals(19.0, $result);
    }

    /**
     * @return void
     */
    public function test_get_net_price_per_unit_returns_correct_value()
    {
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(10.0)
        ;

        $result = $this->sut->getNetPricePerUnit();

        $this->assertEquals(10.0, $result);
    }

    /**
     * @return void
     */
    public function test_get_gross_price_per_unit_returns_correct_value()
    {
        $this->order
            ->method('getInvoiceShipping')
            ->willReturn(11.9)
        ;

        $result = $this->sut->getGrossPricePerUnit();

        $this->assertEquals(11.9, $result);
    }

    /**
     * @return void
     */
    public function test_get_net_position_total_returns_correct_value()
    {
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(10.0)
        ;

        $result = $this->sut->getNetPositionTotal();

        $this->assertEquals(10.0, $result);
    }

    /**
     * @return void
     */
    public function test_get_gross_position_total_returns_correct_value()
    {
        $this->order
            ->method('getInvoiceShipping')
            ->willReturn(11.9)
        ;

        $result = $this->sut->getGrossPositionTotal();

        $this->assertEquals(11.9, $result);
    }
}
