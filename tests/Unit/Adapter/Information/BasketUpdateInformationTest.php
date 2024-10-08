<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\BasketUpdateInformation;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class BasketUpdateInformationTest extends TestCase
{
    /**
     * @var Order&MockObject
     */
    private $order;

    /**
     * @var BasketFactory&MockObject
     */
    private $basketFactory;

    /**
     * @var BasketUpdateInformation
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
        $this->basketFactory = $this->createMock(BasketFactory::class);

        $this->sut = new BasketUpdateInformation(
            $this->order,
            $this->basketFactory
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
    public function test_get_basket_returns_basket()
    {
        $basket = $this->createMock(BasketInterface::class);
        $this->basketFactory
            ->method('create')
            ->with($this->order)
            ->willReturn($basket)
        ;

        $result = $this->sut->getBasket();

        $this->assertSame($basket, $result);
    }
}
