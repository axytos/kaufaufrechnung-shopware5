<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information\Refund;

use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\ShippingBasketPosition;
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

        $this->sut = new ShippingBasketPosition($this->order);
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
    public function test_getNetRefundTotal_returnsCorrectValue()
    {
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(10.0);

        $result = $this->sut->getNetRefundTotal();

        $this->assertEquals(10.0, $result);
    }

    /**
     * @return void
     */
    public function test_getGrossRefundTotal_returnsCorrectValue()
    {
        $this->order
            ->method('getInvoiceShipping')
            ->willReturn(11.9);

        $result = $this->sut->getGrossRefundTotal();

        $this->assertEquals(11.9, $result);
    }
}
