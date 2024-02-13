<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common;

use AxytosKaufAufRechnungShopware5\Adapter\Common\TaxGroup;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail;

class TaxGroupTest extends TestCase
{
    /**
     * @var Detail&MockObject
     */
    private $invoiceItem;

    /**
     * @var TaxGroup
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->invoiceItem = $this->createMock(Detail::class);

        $this->sut = new TaxGroup(
            $this->invoiceItem
        );
    }

    /**
     * @return void
     */
    public function test_getTaxPercent_returnsCorrectValue()
    {
        $this->invoiceItem
            ->method('getTaxRate')
            ->willReturn(19.0);

        $result = $this->sut->getTaxPercent();

        $this->assertEquals(19.0, $result);
    }

    /**
     * @return void
     */
    public function test_getValueToTax_returnsCorrectValue()
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

        $result = $this->sut->getValueToTax();

        $this->assertEquals(300.0, $result);
    }

    /**
     * @return void
     */
    public function test_getTotal_returnsCorrectValue()
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

        $result = $this->sut->getTotal();

        $this->assertEquals(57.0, $result);
    }
}
