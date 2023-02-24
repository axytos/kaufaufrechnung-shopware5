<?php

namespace AxytosKaufAufRechnungShopware5\Tests\ValueCalculation;

use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPricePerUnitCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPricePerUnitCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxPercentCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail as OrderDetail;

class PositionNetPricePerUnitCalculatorTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPricePerUnitCalculator
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->sut = new PositionNetPricePerUnitCalculator(
            new PositionGrossPricePerUnitCalculator(),
            new PositionTaxPercentCalculator()
        );
    }

    /**
     * @dataProvider calculateTestCases
     * @param float $price
     * @param string $taxRate
     * @param float $expected
     * @return void
     */
    public function test_calculate_returns_net_price_for_taxrate($price, $taxRate, $expected)
    {
        /** @var OrderDetail&MockObject */
        $orderDetail = $this->createMock(OrderDetail::class);
        $orderDetail->method('getPrice')->willReturn($price);
        $orderDetail->method('getTaxRate')->willReturn($taxRate);

        $this->assertEquals($expected, $this->sut->calculate($orderDetail));
    }

    /**
     * @return mixed[]
     */
    public function calculateTestCases()
    {
        return [
            [0, '19', 0],
            [0, '7', 0],
            [0, '0', 0],
            [0, '', 0],
            [42, '19', 35.29],
            [42, '7', 39.25],
            [42, '0', 42.00],
            [42, '', 42.00],
            [99.99, '19', 84.03],
            [99.99, '7', 93.45],
            [99.99, '0', 99.99],
            [99.99, '', 99.99]
        ];
    }
}
