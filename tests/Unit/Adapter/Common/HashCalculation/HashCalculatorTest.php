<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common\HashCalculation;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketPositionInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation\HashAlgorithmInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation\HashCalculator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HashCalculatorTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation\HashAlgorithmInterface&MockObject
     */
    private $hashAlgorithm;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation\HashCalculator
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->hashAlgorithm = $this->createMock(HashAlgorithmInterface::class);

        $this->sut = new HashCalculator($this->hashAlgorithm);
    }

    /**
     * @return void
     */
    public function test_calculateBasketHash_calculatesDeterministicHash()
    {
        /** @var BasketPositionInterface&MockObject */
        $position1 = $this->createMock(BasketPositionInterface::class);
        /** @var BasketPositionInterface&MockObject */
        $position2 = $this->createMock(BasketPositionInterface::class);
        /** @var BasketInterface&MockObject */
        $basket = $this->createMock(BasketInterface::class);

        $basket
            ->method('getNetTotal')
            ->willReturn(100.0);
        $basket
            ->method('getGrossTotal')
            ->willReturn(119.0);
        $basket
            ->method('getCurrency')
            ->willReturn('EUR');
        $basket
            ->method('getPositions')
            ->willReturn([$position1, $position2]);

        $position1
            ->method('getProductNumber')
            ->willReturn('prod-1');
        $position1
            ->method('getProductName')
            ->willReturn('Product 1');
        $position1
            ->method('getQuantity')
            ->willReturn(1);
        $position1
            ->method('getTaxPercent')
            ->willReturn(19.0);
        $position1
            ->method('getNetPricePerUnit')
            ->willReturn(10.0);
        $position1
            ->method('getGrossPricePerUnit')
            ->willReturn(11.9);
        $position1
            ->method('getNetPositionTotal')
            ->willReturn(10.0);
        $position1
            ->method('getGrossPositionTotal')
            ->willReturn(11.9);

        $position2
            ->method('getProductNumber')
            ->willReturn('prod-2');
        $position2
            ->method('getProductName')
            ->willReturn('Product 2');
        $position2
            ->method('getQuantity')
            ->willReturn(3);
        $position2
            ->method('getTaxPercent')
            ->willReturn(7.0);
        $position2
            ->method('getNetPricePerUnit')
            ->willReturn(2.0);
        $position2
            ->method('getGrossPricePerUnit')
            ->willReturn(2.1);
        $position2
            ->method('getNetPositionTotal')
            ->willReturn(20.0);
        $position2
            ->method('getGrossPositionTotal')
            ->willReturn(21.4);

        $this->hashAlgorithm
            ->expects($this->once())
            ->method('compute')
            ->with('[100,119,"EUR",[["prod-1","Product 1",null,1,19,10,11.9,10,11.9],["prod-2","Product 2",null,3,7,2,2.1,20,21.4]]]')
            ->willReturn('hash-result');

        $result = $this->sut->calculateBasketHash($basket);

        $this->assertEquals('hash-result', $result);
    }
}
