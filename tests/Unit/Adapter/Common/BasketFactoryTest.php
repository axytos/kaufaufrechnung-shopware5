<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common;

use AxytosKaufAufRechnungShopware5\Adapter\Common\TaxGroupFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\Basket;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketPositionFactory;
use PHPUnit\Framework\TestCase;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use PHPUnit\Framework\Attributes\Before;

class BasketFactoryTest extends TestCase
{
    /**
     * @var BasketFactory
     */
    private $sut;

    /**
     * @return void
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
    public function test_create_returns_instance_of_Basket()
    {
        $basket = $this->sut->create($this->createMock(Order::class));

        $this->assertInstanceOf(Basket::class, $basket);
    }
}
