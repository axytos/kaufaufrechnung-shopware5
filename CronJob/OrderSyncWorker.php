<?php

namespace AxytosKaufAufRechnungShopware5\CronJob;

use Axytos\ECommerce\Logging\LoggerAdapterInterface;

class OrderSyncWorker
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\OrderSyncItemRepository
     */
    private $orderSyncItemRepository;

    /**
     * @var \Axytos\ECommerce\Logging\LoggerAdapterInterface
     */
    private $logger;

    public function __construct(
        OrderSyncItemRepository $orderSyncItemRepository,
        LoggerAdapterInterface $logger
    ) {
        $this->orderSyncItemRepository = $orderSyncItemRepository;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function sync()
    {
        $this->logger->info('OrderSyncWorker started');

        $orderSyncItems = $this->orderSyncItemRepository->getOrdersToSync();

        $this->logger->info('OrderSyncWorker: ' . count($orderSyncItems) . ' to sync.');

        foreach ($orderSyncItems as $orderSyncItem) {
            $orderSyncItem->reportCancel();
            $orderSyncItem->reportCreateInvoice();
            $orderSyncItem->reportRefund();
            $orderSyncItem->reportShipping();
            $orderSyncItem->reportTrackingInformation();
        }

        $this->logger->info('OrderSyncWorker finished');
    }
}
