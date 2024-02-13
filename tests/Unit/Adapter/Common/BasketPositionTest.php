<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common;

use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketPosition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail;

class BasketPositionTest extends TestCase
{
    /**
     * @var Detail&MockObject
     */
    private $orderPosition;

    /**
     * @var BasketPosition
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->orderPosition = $this->createMock(Detail::class);

        $this->sut = new BasketPosition(
            $this->orderPosition
        );
    }

    /**
     * @return void
     */
    public function test_getProductNumber_returnsCorrectValue()
    {
        $this->orderPosition
            ->method('getArticleNumber')
            ->willReturn('art-123');

        $result = $this->sut->getProductNumber();

        $this->assertEquals('art-123', $result);
    }

    /**
     * @return void
     */
    public function test_getProductName_returnsCorrectValue()
    {
        $this->orderPosition
            ->method('getArticleName')
            ->willReturn('Test Article');

        $result = $this->sut->getProductName();

        $this->assertEquals('Test Article', $result);
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
        $this->orderPosition
            ->method('getQuantity')
            ->willReturn(4);

        $result = $this->sut->getQuantity();

        $this->assertEquals(4, $result);
    }

    /**
     * @return void
     */
    public function test_getTaxPercent_returnsCorrectValue()
    {
        $this->orderPosition
            ->method('getTaxRate')
            ->willReturn(19.0);

        $result = $this->sut->getTaxPercent();

        $this->assertEquals(19.0, $result);
    }

    /**
     * @return void
     */
    public function test_getNetPricePerUnit_returnsCorrectValue()
    {
        $this->orderPosition
            ->method('getPrice')
            ->willReturn(119.0);
        $this->orderPosition
            ->method('getTaxRate')
            ->willReturn(19.0);
        $this->orderPosition
            ->method('getQuantity')
            ->willReturn(3);

        $result = $this->sut->getNetPricePerUnit();

        $this->assertEquals(100.0, $result);
    }

    /**
     * @return void
     */
    public function test_getGrossPricePerUnit_returnsCorrectValue()
    {
        $this->orderPosition
            ->method('getPrice')
            ->willReturn(119.0);
        $this->orderPosition
            ->method('getTaxRate')
            ->willReturn(19.0);
        $this->orderPosition
            ->method('getQuantity')
            ->willReturn(3);

        $result = $this->sut->getGrossPricePerUnit();

        $this->assertEquals(119.0, $result);
    }

    /**
     * @return void
     */
    public function test_getNetPositionTotal_returnsCorrectValue()
    {
        $this->orderPosition
            ->method('getPrice')
            ->willReturn(119.0);
        $this->orderPosition
            ->method('getTaxRate')
            ->willReturn(19.0);
        $this->orderPosition
            ->method('getQuantity')
            ->willReturn(3);

        $result = $this->sut->getNetPositionTotal();

        $this->assertEquals(300.0, $result);
    }

    /**
     * @return void
     */
    public function test_getGrossPositionTotal_returnsCorrectValue()
    {
        $this->orderPosition
            ->method('getPrice')
            ->willReturn(119.0);
        $this->orderPosition
            ->method('getTaxRate')
            ->willReturn(19.0);
        $this->orderPosition
            ->method('getQuantity')
            ->willReturn(3);

        $result = $this->sut->getGrossPositionTotal();

        $this->assertEquals(357.0, $result);
    }
}
