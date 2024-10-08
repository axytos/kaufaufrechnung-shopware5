<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common;

use AxytosKaufAufRechnungShopware5\Adapter\Common\ShippingTaxGroup;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
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
     *
     * @before
     */
    #[Before]
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
    public function test_get_value_to_tax_returns_correct_value()
    {
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(10.0)
        ;

        $result = $this->sut->getValueToTax();

        $this->assertEquals(10.0, $result);
    }

    /**
     * @return void
     */
    public function test_get_total_returns_correct_value()
    {
        $this->order
            ->method('getInvoiceShipping')
            ->willReturn(11.9)
        ;
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(10.0)
        ;

        $result = $this->sut->getTotal();

        $this->assertEquals(1.9, $result);
    }
}
