<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\CronJob;

use Axytos\ECommerce\OrderSync\ShopSystemOrderInterface;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderFactory;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderRepository;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Order;

class ShopSystemOrderRepositoryTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository&MockObject
     */
    private $orderRepository;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderFactory&MockObject
     */
    private $shopSystemOrderFactory;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderRepository
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->shopSystemOrderFactory = $this->createMock(ShopSystemOrderFactory::class);

        $this->sut = new ShopSystemOrderRepository(
            $this->orderRepository,
            $this->shopSystemOrderFactory
        );
    }

    /**
     * @return void
     */
    public function test_getOrdersToSync()
    {
        /** @var Order[]&MockObject[] */
        $orders = [
            $this->createMock(Order::class),
            $this->createMock(Order::class)
        ];

        /** @var ShopSystemOrderInterface[]&MockObject[] */
        $shopSystemOrders = [
            $this->createMock(ShopSystemOrderInterface::class),
            $this->createMock(ShopSystemOrderInterface::class)
        ];

        $this->orderRepository->method('getOrdersToSync')->willReturn($orders);
        $this->shopSystemOrderFactory->method('createMany')->with($orders)->willReturn($shopSystemOrders);

        $actual = $this->sut->getOrdersToSync();

        $this->assertSame($shopSystemOrders, $actual);
    }
}
