<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information;

use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Information\CancelInformation;
use AxytosKaufAufRechnungShopware5\Adapter\Information\PaymentInformation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PaymentInformationTest extends TestCase
{
    /**
     * @var Order&MockObject
     */
    private $order;

    /**
     * @var PaymentInformation
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    public function beforeEach()
    {
        $this->order = $this->createMock(Order::class);

        $this->sut = new PaymentInformation($this->order);
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
}
