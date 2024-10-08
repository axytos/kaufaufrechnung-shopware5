<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information\Shipping;

use AxytosKaufAufRechnungShopware5\Adapter\Information\Shipping\BasketPosition;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail;

/**
 * @internal
 */
class BasketPositionTest extends TestCase
{
    /**
     * @var Detail&MockObject
     */
    private $orderDetail;

    /**
     * @var BasketPosition
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
        $this->orderDetail = $this->createMock(Detail::class);

        $this->sut = new BasketPosition($this->orderDetail);
    }

    /**
     * @return void
     */
    public function test_get_product_number_returns_correct_value()
    {
        $this->orderDetail
            ->method('getArticleNumber')
            ->willReturn('art123')
        ;

        $result = $this->sut->getProductNumber();

        $this->assertEquals('art123', $result);
    }

    /**
     * @return void
     */
    public function test_get_quantity_returns_correct_value()
    {
        $this->orderDetail
            ->method('getQuantity')
            ->willReturn(3)
        ;

        $result = $this->sut->getQuantity();

        $this->assertEquals(3, $result);
    }
}
