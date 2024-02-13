<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information;

use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\TrackingInformation;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Tracking\DeliveryAddress;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Dispatch\Dispatch;

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
     * @before
     */
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
    public function test_getOrderNumber_returnsOrderNumber()
    {
        $this->order
            ->method('getNumber')
            ->willReturn('order-123');

        $this->assertEquals('order-123', $this->sut->getOrderNumber());
    }

    /**
     * @return void
     */
    public function test_getDeliveryWeight_alwaysReturnsZero()
    {
        $result = $this->sut->getDeliveryWeight();

        $this->assertEquals(0, $result);
    }

    /**
     * @return void
     */
    public function test_getDeliveryMethod_returnsNameOfDispatch()
    {
        /** @var Dispatch&MockObject */
        $dispatch = $this->createMock(Dispatch::class);
        $this->order
            ->method('getDispatch')
            ->willReturn($dispatch);
        $dispatch
            ->method('getName')
            ->willReturn('Logistikunternehmen');

        $result = $this->sut->getDeliveryMethod();

        $this->assertEquals('Logistikunternehmen', $result);
    }

    /**
     * @return void
     */
    public function test_getDeliveryAddress_returnsDeliveryAdressAdapter()
    {
        $result = $this->sut->getDeliveryAddress();

        $this->assertInstanceOf(DeliveryAddress::class, $result);
    }

    /**
     * @return void
     */
    public function test_getTrackingIds_returnsArrayWithSingleTrackingId()
    {
        $this->order
            ->method('getTrackingCode')
            ->willReturn('tracking-code');

        $result = $this->sut->getTrackingIds();

        $this->assertCount(1, $result);
        $this->assertEquals('tracking-code', $result[0]);
    }

    /**
     * @return void
     */
    public function test_getTrackingIds_returnsEmptyArrayIfNoTrackingIdIsAvailable()
    {
        $this->order
            ->method('getTrackingCode')
            ->willReturn(null);

        $result = $this->sut->getTrackingIds();

        $this->assertEmpty($result);
    }
}
