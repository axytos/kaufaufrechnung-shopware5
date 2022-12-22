<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob;

use Axytos\ECommerce\Clients\ErrorReporting\ErrorReportingClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItem;
use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemFactory;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderSyncItemFactoryTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemFactory
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->sut = new OrderSyncItemFactory(
            $this->createMock(InvoiceClientInterface::class),
            $this->createMock(ErrorReportingClientInterface::class),
            $this->createMock(LoggerAdapterInterface::class)
        );
    }

    /**
     * @return void
     */
    public function test_create()
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $orderSyncItem = $this->sut->create($shopSystemOrder);

        $this->assertInstanceOf(OrderSyncItem::class, $orderSyncItem);
    }

    /**
     * @return void
     */
    public function test_createMany()
    {
        /** @var ShopSystemOrderInterface[]&MockObject[] */
        $shopSystemOrders = [
            $this->createMock(ShopSystemOrderInterface::class),
            $this->createMock(ShopSystemOrderInterface::class),
            $this->createMock(ShopSystemOrderInterface::class),
            $this->createMock(ShopSystemOrderInterface::class),
        ];

        $orderSyncItems = $this->sut->createMany($shopSystemOrders);

        $this->assertCount(4, $orderSyncItems);

        foreach ($orderSyncItems as $orderSyncItems) {
            $this->assertInstanceOf(OrderSyncItem::class, $orderSyncItems);
        }
    }
}
