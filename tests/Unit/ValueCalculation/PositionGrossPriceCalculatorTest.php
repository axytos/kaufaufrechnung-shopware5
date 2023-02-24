<?php

namespace AxytosKaufAufRechnungShopware5\Tests\ValueCalculation;

use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPricePerUnitCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail as OrderDetail;

class PositionGrossPriceCalculatorTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPriceCalculator
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->sut = new PositionGrossPriceCalculator(
            new PositionGrossPricePerUnitCalculator(),
            new PositionQuantityCalculator()
        );
    }

    /**
     * @dataProvider calculateTestCases
     * @param float $price
     * @param int $quantity
     * @param float $expected
     * @return void
     */
    public function test_returns_price_for_quantity($price, $quantity, $expected)
    {
        /** @var OrderDetail&MockObject */
        $orderDetail = $this->createMock(OrderDetail::class);
        $orderDetail->method('getPrice')->willReturn($price);
        $orderDetail->method('getQuantity')->willReturn($quantity);

        $this->assertEquals($expected, $this->sut->calculate($orderDetail));
    }

    /**
     * @return mixed[]
     */
    public function calculateTestCases()
    {
        return [
            [42, 0, 0],
            [0, 1, 0],
            [42, 1, 42],
            [1.89, 3, 5.67],
            [299.99, 5, 1499.95]
        ];
    }
}
