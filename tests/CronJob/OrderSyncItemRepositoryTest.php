<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob;

use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemFactory;
use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemInterface;
use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemRepository;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderInterface;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderSyncItemRepositoryTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderRepository&MockObject
     */
    private $shopSystemOrderRepository;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemFactory&MockObject
     */
    private $orderSyncItemFactory;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemRepository
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->shopSystemOrderRepository = $this->createMock(ShopSystemOrderRepository::class);
        $this->orderSyncItemFactory = $this->createMock(OrderSyncItemFactory::class);

        $this->sut = new OrderSyncItemRepository(
            $this->shopSystemOrderRepository,
            $this->orderSyncItemFactory
        );
    }

    /**
     * @return void
     */
    public function test_getOrdersToSync()
    {
        /** @var ShopSystemOrderInterface[]&MockObject[] */
        $shopSystemOrders = [
            $this->createMock(ShopSystemOrderInterface::class),
            $this->createMock(ShopSystemOrderInterface::class)
        ];

        /** @var OrderSyncItemInterface[]&MockObject[] */
        $orderSyncItems = [
            $this->createMock(OrderSyncItemInterface::class),
            $this->createMock(OrderSyncItemInterface::class)
        ];

        $this->shopSystemOrderRepository->method('getOrdersToSync')->willReturn($shopSystemOrders);
        $this->orderSyncItemFactory->method('createMany')->with($shopSystemOrders)->willReturn($orderSyncItems);

        $actual = $this->sut->getOrdersToSync();

        $this->assertSame($orderSyncItems, $actual);
    }
}
