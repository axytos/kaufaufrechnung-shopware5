<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common;

use AxytosKaufAufRechnungShopware5\Adapter\Common\Basket;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketPositionFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\TaxGroupFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail;

/**
 * @internal
 */
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
     *
     * @before
     */
    #[Before]
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
    public function test_get_net_total_returns_correct_value()
    {
        $this->order
            ->method('getInvoiceAmountNet')
            ->willReturn(100.00)
        ;

        $result = $this->sut->getNetTotal();

        $this->assertEquals(100.00, $result);
    }

    /**
     * @return void
     */
    public function test_get_currency_returns_correct_value()
    {
        $this->order
            ->method('getCurrency')
            ->willReturn('EUR')
        ;

        $result = $this->sut->getCurrency();

        $this->assertEquals('EUR', $result);
    }

    /**
     * @return void
     */
    public function test_get_gross_total_returns_correct_value()
    {
        $this->order
            ->method('getInvoiceAmount')
            ->willReturn(119.00)
        ;

        $result = $this->sut->getGrossTotal();

        $this->assertEquals(119.00, $result);
    }

    /**
     * @return void
     */
    public function test_get_positions_uses_factory()
    {
        /** @var Detail&MockObject */
        $detail1 = $this->createMock(Detail::class);
        $detail1
            ->method('getArticleNumber')
            ->willReturn('art-1')
        ;

        /** @var Detail&MockObject */
        $detail2 = $this->createMock(Detail::class);
        $detail2
            ->method('getArticleNumber')
            ->willReturn('art-2')
        ;

        $this->order
            ->method('getInvoiceShipping')
            ->willReturn(11.9)
        ;
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(10.0)
        ;
        $this->order
            ->method('getInvoiceShippingTaxRate')
            ->willReturn(19.0)
        ;
        $this->order
            ->method('getDetails')
            ->willReturn(new ArrayCollection([$detail1, $detail2]))
        ;

        $result = $this->sut->getPositions();

        $this->assertCount(3, $result);
        $this->assertEquals('art-1', $result[0]->getProductNumber());
        $this->assertEquals('art-2', $result[1]->getProductNumber());

        $shippingPosition = $result[2];
        $this->assertEquals('0', $shippingPosition->getProductNumber());
        $this->assertEquals('Shipping', $shippingPosition->getProductName());
        $this->assertNull($shippingPosition->getProductCategory());
        $this->assertEquals(1, $shippingPosition->getQuantity());
        $this->assertEquals(19.0, $shippingPosition->getTaxPercent());
        $this->assertEquals(10.0, $shippingPosition->getNetPricePerUnit());
        $this->assertEquals(11.9, $shippingPosition->getGrossPricePerUnit());
        $this->assertEquals(10.0, $shippingPosition->getNetPositionTotal());
        $this->assertEquals(11.9, $shippingPosition->getGrossPositionTotal());
    }

    /**
     * @return void
     */
    public function test_get_tax_groups_uses_factory()
    {
        /** @var Detail&MockObject */
        $detail1 = $this->createMock(Detail::class);
        $detail1
            ->method('getPrice')
            ->willReturn(119.0)
        ;
        $detail1
            ->method('getTaxRate')
            ->willReturn(19.0)
        ;
        $detail1
            ->method('getQuantity')
            ->willReturn(1)
        ;

        /** @var Detail&MockObject */
        $detail2 = $this->createMock(Detail::class);
        $detail2
            ->method('getPrice')
            ->willReturn(10.7)
        ;
        $detail2
            ->method('getTaxRate')
            ->willReturn(7.0)
        ;
        $detail2
            ->method('getQuantity')
            ->willReturn(3)
        ;

        $this->order
            ->method('getInvoiceShippingTaxRate')
            ->willReturn(19.0)
        ;
        $this->order
            ->method('getInvoiceShipping')
            ->willReturn(5.95)
        ;
        $this->order
            ->method('getInvoiceShippingNet')
            ->willReturn(5.0)
        ;
        $this->order
            ->method('getDetails')
            ->willReturn(new ArrayCollection([$detail1, $detail2]))
        ;

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
