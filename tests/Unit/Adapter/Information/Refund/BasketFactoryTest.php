<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information\Refund;

use AxytosKaufAufRechnungShopware5\Adapter\Common\TaxGroupFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\Basket;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketPositionFactory;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class BasketFactoryTest extends TestCase
{
    /**
     * @var BasketFactory
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
        $this->sut = new BasketFactory(
            $this->createMock(BasketPositionFactory::class),
            $this->createMock(TaxGroupFactory::class)
        );
    }

    /**
     * @return void
     */
    public function test_create_returns_instance_of_basket()
    {
        $basket = $this->sut->create($this->createMock(Order::class));

        $this->assertInstanceOf(Basket::class, $basket);
    }
}
