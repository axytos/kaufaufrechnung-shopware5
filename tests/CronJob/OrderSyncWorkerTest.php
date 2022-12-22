<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob;

use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemInterface;
use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemRepository;
use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncWorker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrderSyncWorkerTest extends TestCase
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemRepository&MockObject
     */
    private $orderSyncItemRepository;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\OrderSyncWorker
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->orderSyncItemRepository = $this->createMock(OrderSyncItemRepository::class);

        $this->sut = new OrderSyncWorker(
            $this->orderSyncItemRepository,
            $this->createMock(LoggerAdapterInterface::class)
        );
    }

    /**
     * @return void
     */
    public function test_sync_executes_command_for_each_order()
    {
        /** @var OrderSyncItemInterface[]&MockObject[] */
        $orderSyncItems = [
            $this->createMock(OrderSyncItemInterface::class),
            $this->createMock(OrderSyncItemInterface::class),
            $this->createMock(OrderSyncItemInterface::class),
            $this->createMock(OrderSyncItemInterface::class),
            $this->createMock(OrderSyncItemInterface::class),
        ];

        $this->orderSyncItemRepository->method('getOrdersToSync')->willReturn($orderSyncItems);

        $executionCounts = [
            'reportCancel' => 0,
            'reportCreateInvoice' => 0,
            'reportRefund' => 0,
            'reportShipping' => 0,
            'reportTrackingInformation' => 0,
        ];

        foreach ($orderSyncItems as $orderSyncItem) {
            $orderSyncItem->method('reportCancel')->willReturnCallback(function () use (&$executionCounts) {
                $executionCounts['reportCancel']++;
            });
            $orderSyncItem->method('reportCreateInvoice')->willReturnCallback(function () use (&$executionCounts) {
                $executionCounts['reportCreateInvoice']++;
            });
            $orderSyncItem->method('reportRefund')->willReturnCallback(function () use (&$executionCounts) {
                $executionCounts['reportRefund']++;
            });
            $orderSyncItem->method('reportShipping')->willReturnCallback(function () use (&$executionCounts) {
                $executionCounts['reportShipping']++;
            });
            $orderSyncItem->method('reportTrackingInformation')->willReturnCallback(function () use (&$executionCounts) {
                $executionCounts['reportTrackingInformation']++;
            });
        }

        $this->sut->sync();

        $this->assertEquals(5, $executionCounts['reportCancel']);
        $this->assertEquals(5, $executionCounts['reportCreateInvoice']);
        $this->assertEquals(5, $executionCounts['reportRefund']);
        $this->assertEquals(5, $executionCounts['reportShipping']);
        $this->assertEquals(5, $executionCounts['reportTrackingInformation']);
    }
}
