<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information\Refund;

use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketPosition;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail;

class BasketPositionTest extends TestCase
{
    /**
     * @var Detail&MockObject
     */
    private $invoiceItem;

    /**
     * @var BasketPosition
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    #[Before]
    public function beforeEach()
    {
        $this->invoiceItem = $this->createMock(Detail::class);

        $this->sut = new BasketPosition(
            $this->invoiceItem
        );
    }

    /**
     * @return void
     */
    public function test_getProductNumber_returnsCorrectValue()
    {
        $this->invoiceItem
            ->method('getArticleNumber')
            ->willReturn('art123');

        $result = $this->sut->getProductNumber();

        $this->assertEquals('art123', $result);
    }

    /**
     * @return void
     */
    public function test_getNetRefundTotal_returnsCorrectValue()
    {
        $this->invoiceItem
            ->method('getPrice')
            ->willReturn(119.0);
        $this->invoiceItem
            ->method('getTaxRate')
            ->willReturn(19.0);
        $this->invoiceItem
            ->method('getQuantity')
            ->willReturn(3);

        $result = $this->sut->getNetRefundTotal();

        $this->assertEquals(300.0, $result);
    }

    /**
     * @return void
     */
    public function test_getGrossRefundTotal_returnsCorrectValue()
    {
        $this->invoiceItem
            ->method('getPrice')
            ->willReturn(119.0);
        $this->invoiceItem
            ->method('getTaxRate')
            ->willReturn(19.0);
        $this->invoiceItem
            ->method('getQuantity')
            ->willReturn(3);

        $result = $this->sut->getGrossRefundTotal();

        $this->assertEquals(357.0, $result);
    }
}
