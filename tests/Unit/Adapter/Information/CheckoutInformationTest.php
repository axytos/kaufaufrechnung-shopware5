<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information;

use AxytosKaufAufRechnungShopware5\Adapter\Common\Basket;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\OrderAttributes;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Checkout\Customer as CheckoutCustomer;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Checkout\DeliveryAddress;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Checkout\InvoiceAddress;
use AxytosKaufAufRechnungShopware5\Adapter\Information\CheckoutInformation;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Order\Billing;
use Shopware\Models\Order\Shipping;

class CheckoutInformationTest extends TestCase
{
    /**
     * @var Order&MockObject
     */
    private $order;

    /**
     * @var OrderAttributes&MockObject
     */
    private $orderAttributes;

    /**
     * @var BasketFactory&MockObject
     */
    private $basketFactory;

    /**
     * @var CheckoutInformation
     */
    private $sut;

    /**
     * @return void
     * @before
     */
    #[Before]
    public function beforeEach()
    {
        $this->order = $this->createMock(Order::class);
        $this->orderAttributes = $this->createMock(OrderAttributes::class);
        $this->basketFactory = $this->createMock(BasketFactory::class);

        $this->order
            ->method('getAttributes')
            ->willReturn($this->orderAttributes);

        $this->sut = new CheckoutInformation(
            $this->order,
            $this->basketFactory
        );
    }

    /**
     * @return void
     */
    public function test_savePreCheckResponseData_persistsDataAsAttribute()
    {
        $precheckData = [
            'a' => 1,
            'b' => [4.5, true],
        ];
        $precheckDataJson = json_encode($precheckData);

        $this->orderAttributes
            ->expects($this->once())
            ->method('setAxytosKaufAufRechnungPrecheckResponse')
            ->with($precheckDataJson);
        $this->orderAttributes
            ->expects($this->once())
            ->method('persist');

        $this->sut->savePreCheckResponseData($precheckData);
    }

    /**
     * @return void
     */
    public function test_getPreCheckResponseData_loadsDataFromAttribute()
    {
        $precheckData = [
            'a' => 1,
            'b' => [4.5, true],
        ];
        $precheckDataJson = json_encode($precheckData);

        $this->orderAttributes
            ->expects($this->once())
            ->method('getAxytosKaufAufRechnungPrecheckResponse')
            ->willReturn($precheckDataJson);

        $result = $this->sut->getPreCheckResponseData();

        $this->assertEquals($precheckData, $result);
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
    public function test_getCustomer_returnsCustomer()
    {
        $customer = $this->createMock(Customer::class);

        $this->order
            ->method('getCustomer')
            ->willReturn($customer);

        $result = $this->sut->getCustomer();

        $this->assertInstanceOf(CheckoutCustomer::class, $result);
    }

    /**
     * @return void
     */
    public function test_getInvoiceAddress_returnsInvoiceAddress()
    {
        $billing = $this->createMock(Billing::class);

        $this->order
            ->method('getBilling')
            ->willReturn($billing);

        $result = $this->sut->getInvoiceAddress();

        $this->assertInstanceOf(InvoiceAddress::class, $result);
    }

    /**
     * @return void
     */
    public function test_getDeliveryAddress_returnsDeliveryAddress()
    {
        $shipping = $this->createMock(Shipping::class);

        $this->order
            ->method('getShipping')
            ->willReturn($shipping);

        $result = $this->sut->getDeliveryAddress();

        $this->assertInstanceOf(DeliveryAddress::class, $result);
    }

    /**
     * @return void
     */
    public function test_getBasket_returns_basket()
    {
        $expected = $this->createMock(Basket::class);

        $this->basketFactory
            ->method('create')
            ->with($this->order)
            ->willReturn($expected);

        $basket = $this->sut->getBasket();

        $this->assertSame($expected, $basket);
    }
}
