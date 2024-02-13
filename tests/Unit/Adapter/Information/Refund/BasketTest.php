<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information\Refund;

use AxytosKaufAufRechnungShopware5\Adapter\Common\TaxGroupFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\Basket;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketPositionFactory;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class BasketTest extends TestCase
{
    /**
     * @var Order&MockObject
     */
    private $order;

    /**
     * @var Basket
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->order = $this->createMock(Order::class);

        $this->sut = new Basket(
            new BasketPositionFactory(),
            new TaxGroupFactory(),
            $this->order
        );
    }

    /**
     * @return void
     */
    public function test_getNetTotal_returnsCorrectValue()
    {
        $this->order
            ->method('getDetails')
            ->willReturn(new ArrayCollection());
        $this->order
            ->method('getInvoiceAmountNet')
            ->willReturn(100.00);

        $result = $this->sut->getNetTotal();

        $this->assertEquals(100.00, $result);
    }

    /**
     * @return void
     */
    public function test_getGrossTotal_returnsCorrectValue()
    {
        $this->order
            ->method('getDetails')
            ->willReturn(new ArrayCollection());
        $this->order
            ->method('getInvoiceAmount')
            ->willReturn(119.00);

        $result = $this->sut->getGrossTotal();

        $this->assertEquals(119.00, $result);
    }

    /**
     * @return void
     */
    public function test_getPositions_usesFactory()
    {
        /** @var Detail&MockObject */
        $detail1 = $this->createMock(Detail::class);
        $detail1
            ->method('getArticleNumber')
            ->willReturn('art-1');

        /** @var Detail&MockObject */
        $detail2 = $this->createMock(Detail::class);
        $detail2
            ->method('getArticleNumber')
            ->willReturn('art-2');

        $this->order
            ->method('getDetails')
            ->willReturn(new ArrayCollection([$detail1, $detail2]));

        $result = $this->sut->getPositions();

        $this->assertCount(3, $result);
        $this->assertEquals('art-1', $result[0]->getProductNumber());
        $this->assertEquals('art-2', $result[1]->getProductNumber());
        $this->assertEquals('0', $result[2]->getProductNumber());
    }

    /**
     * @return void
     */
    public function test_getTaxGroups_usesFactory()
    {
        /** @var Detail&MockObject */
        $detail1 = $this->createMock(Detail::class);
        $detail1
            ->method('getPrice')
            ->willReturn(119.0);
        $detail1
            ->method('getTaxRate')
            ->willReturn(19.0);
        $detail1
            ->method('getQuantity')
            ->willReturn(1);

        /** @var Detail&MockObject */
        $detail2 = $this->createMock(Detail::class);
        $detail2
            ->method('getPrice')
            ->willReturn(10.7);
        $detail2
            ->method('getTaxRate')
            ->willReturn(7.0);
        $detail2
            ->method('getQuantity')
            ->willReturn(3);

        $this->order
            ->method('getInvoiceShippingTaxRate')
            ->willReturn(19.0);
        $this->order
            ->method('getInvoiceShipping')
            ->willReturn(5.95);
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(5.0);
        $this->order
            ->method('getDetails')
            ->willReturn(new ArrayCollection([$detail1, $detail2]));

        $result = $this->sut->getTaxGroups();

        $this->assertCount(2, $result);
        $this->assertEquals(19.0, $result[0]->getTaxPercent());
        $this->assertEquals(105.0, $result[0]->getValueToTax());
        $this->assertEquals(19.95, $result[0]->getTotal());
        $this->assertEquals(7.0, $result[1]->getTaxPercent());
        $this->assertEquals(30.0, $result[1]->getValueToTax());
        $this->assertEquals(2.1, $result[1]->getTotal());
    }
}
