<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information;

use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Tracking\DeliveryAddress;
use AxytosKaufAufRechnungShopware5\Adapter\Information\TrackingInformation;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Dispatch\Dispatch;

/**
 * @internal
 */
class TrackingInformationTest extends TestCase
{
    /**
     * @var Order&MockObject
     */
    private $order;

    /**
     * @var TrackingInformation
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

        $this->sut = new TrackingInformation(
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
    public function test_get_delivery_weight_always_returns_zero()
    {
        $result = $this->sut->getDeliveryWeight();

        $this->assertEquals(0, $result);
    }

    /**
     * @return void
     */
    public function test_get_delivery_method_returns_name_of_dispatch()
    {
        /** @var Dispatch&MockObject */
        $dispatch = $this->createMock(Dispatch::class);
        $this->order
            ->method('getDispatch')
            ->willReturn($dispatch)
        ;
        $dispatch
            ->method('getName')
            ->willReturn('Logistikunternehmen')
        ;

        $result = $this->sut->getDeliveryMethod();

        $this->assertEquals('Logistikunternehmen', $result);
    }

    /**
     * @return void
     */
    public function test_get_delivery_address_returns_delivery_adress_adapter()
    {
        $result = $this->sut->getDeliveryAddress();

        $this->assertInstanceOf(DeliveryAddress::class, $result);
    }

    /**
     * @return void
     */
    public function test_get_tracking_ids_returns_array_with_single_tracking_id()
    {
        $this->order
            ->method('getTrackingCode')
            ->willReturn('tracking-code')
        ;

        $result = $this->sut->getTrackingIds();

        $this->assertCount(1, $result);
        $this->assertEquals('tracking-code', $result[0]);
    }

    /**
     * @return void
     */
    public function test_get_tracking_ids_returns_empty_array_if_no_tracking_id_is_available()
    {
        $this->order
            ->method('getTrackingCode')
            ->willReturn(null)
        ;

        $result = $this->sut->getTrackingIds();

        $this->assertEmpty($result);
    }
}
