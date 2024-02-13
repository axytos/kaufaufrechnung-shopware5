<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common;

use AxytosKaufAufRechnungShopware5\Adapter\Common\ShippingTaxGroup;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class ShippingTaxGroupTest extends TestCase
{
    /**
     * @var Order&MockObject
     */
    private $order;

    /**
     * @var ShippingTaxGroup
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->order = $this->createMock(Order::class);

        $this->sut = new ShippingTaxGroup(
            $this->order
        );
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
    public function test_getValueToTax_returnsCorrectValue()
    {
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(10.0);

        $result = $this->sut->getValueToTax();

        $this->assertEquals(10.0, $result);
    }

    /**
     * @return void
     */
    public function test_getTotal_returnsCorrectValue()
    {
        $this->order
            ->method('getInvoiceShipping')
            ->willReturn(11.9);
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(10.0);

        $result = $this->sut->getTotal();

        $this->assertEquals(1.9, $result);
    }
}
