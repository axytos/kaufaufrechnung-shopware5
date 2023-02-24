<?php

namespace AxytosKaufAufRechnungShopware5\Tests\ValueCalculation;

use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPricePerUnitCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail as OrderDetail;

class PositionGrossPricePerUnitCalculatorTest extends TestCase
{
    /**
     * @return void
     */
    public function test_returns_price_order_detail()
    {
        $sut = new PositionGrossPricePerUnitCalculator();

        /** @var OrderDetail&MockObject */
        $orderDetail = $this->createMock(OrderDetail::class);
        $orderDetail->method('getPrice')->willReturn(42);

        $this->assertEquals(42, $sut->calculate($orderDetail));
    }
}
