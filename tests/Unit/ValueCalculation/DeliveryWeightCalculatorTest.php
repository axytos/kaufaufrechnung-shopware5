<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\ValueCalculation;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\ArticleDetailRepository;
use AxytosKaufAufRechnungShopware5\ValueCalculation\DeliveryWeightCalculator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Detail as OrderDetail;
use Shopware\Models\Order\Order;

class DeliveryWeightCalculatorTest extends TestCase
{
    /** @var ArticleDetailRepository&MockObject */
    private $articleDetailRepository;

    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\DeliveryWeightCalculator
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->articleDetailRepository = $this->createMock(ArticleDetailRepository::class);

        $this->sut = new DeliveryWeightCalculator($this->articleDetailRepository);
    }

    /**
     * @dataProvider calculateTestCases
     * @param float $expectedDeliveryWeight
     * @param array<float[]> $orderDetailSpecs
     * @return void
     */
    public function test_calculate($expectedDeliveryWeight, $orderDetailSpecs)
    {
        $orderDetails = new ArrayCollection();
        $orderDetailWeightReturnMap = [];
        foreach ($orderDetailSpecs as $orderDetailSpec) {
            list($quantity, $weight) = $orderDetailSpec;

            /** @var OrderDetail&MockObject */
            $orderDetail = $this->createMock(OrderDetail::class);
            $orderDetail->method('getQuantity')->willReturn($quantity);

            array_push($orderDetailWeightReturnMap, [$orderDetail, $weight]);

            $orderDetails->add($orderDetail);
        }

        $this->articleDetailRepository
                ->method('findArticleWeightForOrderDetail')
                ->willReturnMap($orderDetailWeightReturnMap);

        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);
        $order->method('getDetails')->willReturn($orderDetails);

        $this->assertEquals($expectedDeliveryWeight, $this->sut->calculate($order));
    }

    /**
     * @return mixed[]
     */
    public function calculateTestCases()
    {
        return [
            [0.0, []],

            [0.0, [[1,0.0]]],
            [1.0, [[1,1.0]]],
            [1.42, [[1,1.42]]],
            [2.84, [[2,1.42]]],

            [1.0, [[1,0.0],[1,1.0]]],
            [1.5, [[1,0.0],[1,1.5]]],
            [4.5, [[1,0.0],[1,1.5],[2,1.5]]],
        ];
    }
}
