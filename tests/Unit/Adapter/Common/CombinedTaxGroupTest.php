<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\TaxGroupInterface as InvoiceTaxGroupInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\TaxGroupInterface as RefundTaxGroupInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\CombinedTaxGroup;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class TestTaxGroup implements InvoiceTaxGroupInterface, RefundTaxGroupInterface
{
}


class CombinedTaxGroupTest extends TestCase
{
    /**
     * @var InvoiceTaxGroupInterface&RefundTaxGroupInterface&MockObject
     */
    private $taxGroup1;

    /**
     * @var InvoiceTaxGroupInterface&RefundTaxGroupInterface&MockObject
     */
    private $taxGroup2;

    /**
     * @var InvoiceTaxGroupInterface&RefundTaxGroupInterface&MockObject
     */
    private $taxGroup3;

    /**
     * @var CombinedTaxGroup
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->taxGroup1 = $this->createMock(TestTaxGroup::class);
        $this->taxGroup2 = $this->createMock(TestTaxGroup::class);
        $this->taxGroup3 = $this->createMock(TestTaxGroup::class);

        $this->sut = new CombinedTaxGroup($this->taxGroup1);
        $this->sut->addTaxGroup($this->taxGroup2);
        $this->sut->addTaxGroup($this->taxGroup3);
    }

    /**
     * @return void
     */
    public function test_getTaxPercent_returnsCorrectValue()
    {
        $this->taxGroup1
            ->method('getTaxPercent')
            ->willReturn(19.0);
        $this->taxGroup2
            ->method('getTaxPercent')
            ->willReturn(30.0);
        $this->taxGroup3
            ->method('getTaxPercent')
            ->willReturn(40.0);

        $result = $this->sut->getTaxPercent();

        $this->assertEquals(19.0, $result);
    }

    /**
     * @return void
     */
    public function test_getValueToTax_returnsCorrectValue()
    {
        $this->taxGroup1
            ->method('getValueToTax')
            ->willReturn(10.333);
        $this->taxGroup2
            ->method('getValueToTax')
            ->willReturn(15.333);
        $this->taxGroup3
            ->method('getValueToTax')
            ->willReturn(20.333);

        $result = $this->sut->getValueToTax();

        $this->assertEquals(46.0, $result);
    }

    /**
     * @return void
     */
    public function test_getTotal_returnsCorrectValue()
    {
        $this->taxGroup1
            ->method('getTotal')
            ->willReturn(5.033);
        $this->taxGroup2
            ->method('getTotal')
            ->willReturn(10.033);
        $this->taxGroup3
            ->method('getTotal')
            ->willReturn(5.033);

        $result = $this->sut->getTotal();

        $this->assertEquals(20.1, $result);
    }
}
