<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob;

use AxytosKaufAufRechnungShopware5\Core\InvoiceOrderContextFactory;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrder;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderFactory;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order;

class ShopSystemOrderFactoryTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderFactory
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->sut = new ShopSystemOrderFactory(
            $this->createMock(OrderRepository::class),
            $this->createMock(InvoiceOrderContextFactory::class)
        );
    }

    /**
     * @return void
     */
    public function test_create()
    {
        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);

        $shopSystemOrder = $this->sut->create($order);

        $this->assertInstanceOf(ShopSystemOrder::class, $shopSystemOrder);
    }

    /**
     * @return void
     */
    public function test_createMany()
    {
        /** @var Order[]&MockObject[] */
        $orders = [
            $this->createMock(Order::class),
            $this->createMock(Order::class),
            $this->createMock(Order::class),
            $this->createMock(Order::class),
        ];

        $shopSystemOrders = $this->sut->createMany($orders);

        $this->assertCount(4, $shopSystemOrders);

        foreach ($shopSystemOrders as $shopSystemOrder) {
            $this->assertInstanceOf(ShopSystemOrder::class, $shopSystemOrder);
        }
    }
}
