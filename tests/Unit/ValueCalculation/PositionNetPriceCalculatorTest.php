<?php

namespace AxytosKaufAufRechnungShopware5\Tests\ValueCalculation;

use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPricePerUnitCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPricePerUnitCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxPercentCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail as OrderDetail;

class PositionNetPriceCalculatorTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->sut = new PositionNetPriceCalculator(
            new PositionNetPricePerUnitCalculator(
                new PositionGrossPricePerUnitCalculator(),
                new PositionTaxPercentCalculator()
            ),
            new PositionQuantityCalculator()
        );
    }

    /**
     * @dataProvider calculateTestCases
     * @param float $price
     * @param int $quantity
     * @param string $taxRate
     * @param float $expected
     * @return void
     */
    public function test_calculate_returns_net_price_for_taxrate_and_quantity($price, $quantity, $taxRate, $expected)
    {
        /** @var OrderDetail&MockObject */
        $orderDetail = $this->createMock(OrderDetail::class);
        $orderDetail->method('getPrice')->willReturn($price);
        $orderDetail->method('getQuantity')->willReturn($quantity);
        $orderDetail->method('getTaxRate')->willReturn($taxRate);

        $this->assertEquals($expected, $this->sut->calculate($orderDetail));
    }

    /**
     * @return mixed[]
     */
    public function calculateTestCases()
    {
        return [
            [0, 0, '19', 0],
            [0, 0, '7', 0],
            [0, 0, '0', 0],
            [0, 0, '', 0],
            [42, 0, '19', 0],
            [42, 0, '7', 0],
            [42, 0, '0', 0],
            [42, 0, '', 0],
            [42, 1, '19', 35.29],
            [42, 1, '7', 39.25],
            [42, 1, '0', 42.00],
            [42, 1, '', 42.00],
            [99.99, 3, '19', 252.09],
            [99.99, 3, '7', 280.35],
            [99.99, 3, '0', 299.97],
            [99.99, 3, '', 299.97]
        ];
    }
}
