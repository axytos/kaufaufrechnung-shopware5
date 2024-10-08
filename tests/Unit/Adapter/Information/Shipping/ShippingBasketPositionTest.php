<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information\Shipping;

use AxytosKaufAufRechnungShopware5\Adapter\Information\Shipping\ShippingBasketPosition;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ShippingBasketPositionTest extends TestCase
{
    /**
     * @var ShippingBasketPosition
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
        $this->sut = new ShippingBasketPosition();
    }

    /**
     * @return void
     */
    public function test_get_product_number_returns_correct_value()
    {
        $result = $this->sut->getProductNumber();

        $this->assertEquals('0', $result);
    }

    /**
     * @return void
     */
    public function test_get_quantity_returns_correct_value()
    {
        $result = $this->sut->getQuantity();

        $this->assertEquals(1, $result);
    }
}
