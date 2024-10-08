<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information;

use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Shipping\BasketPosition;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Shipping\ShippingBasketPosition;
use AxytosKaufAufRechnungShopware5\Adapter\Information\ShippingInformation;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail;

/**
 * @internal
 */
class ShippingInformationTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order&MockObject
     */
    private $order;

    /**
     * @var ShippingInformation
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

        $this->sut = new ShippingInformation(
            $this->order
        );
    }

    /**
     * @return void
     */
    public function test_get_order_number_returns_order_number()
    {
        $this->order
            ->method('getNumber')
            ->willReturn('order-123')
        ;

        $this->assertEquals('order-123', $this->sut->getOrderNumber());
    }

    /**
     * @return void
     */
    public function test_get_shipping_basket_positions_creates_basket_positions()
    {
        $details = new ArrayCollection();
        $details->add($this->createMock(Detail::class));
        $details->add($this->createMock(Detail::class));
        $details->add($this->createMock(Detail::class));

        $this->order->method('getDetails')->willReturn($details);

        $shippingBasketPositions = $this->sut->getShippingBasketPositions();

        $basketPositionCount = count(array_filter($shippingBasketPositions, function ($position) {
            return $position instanceof BasketPosition;
        }));

        $this->assertEquals(3, $basketPositionCount);
    }

    /**
     * @return void
     */
    public function test_get_shipping_basket_positions_contains_shipping()
    {
        $details = new ArrayCollection();
        $details->add($this->createMock(Detail::class));
        $details->add($this->createMock(Detail::class));
        $details->add($this->createMock(Detail::class));

        $this->order->method('getDetails')->willReturn($details);

        $shippingBasketPositions = $this->sut->getShippingBasketPositions();

        $shippingPositionCount = count(array_filter($shippingBasketPositions, function ($position) {
            return $position instanceof ShippingBasketPosition;
        }));

        $this->assertEquals(4, count($shippingBasketPositions));
        $this->assertEquals(1, $shippingPositionCount);
    }
}
