<?php

namespace AxytosKaufAufRechnungShopware5\CronJob\Commands;

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncCommandInterface;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderInterface;

class ReportShipping implements OrderSyncCommandInterface
{
    /**
     * @var \Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface
     */
    private $invoiceClient;

    /**
     * @var \Axytos\ECommerce\Logging\LoggerAdapterInterface
     */
    private $logger;

    public function __construct(
        InvoiceClientInterface $invoiceClient,
        LoggerAdapterInterface $logger
    ) {
        $this->invoiceClient = $invoiceClient;
        $this->logger = $logger;
    }

    /**
     * @param ShopSystemOrderInterface $shopSystemOrder
     * @return void
     */
    public function execute($shopSystemOrder)
    {
        if ($shopSystemOrder->hasShippingReported()) {
            return;
        }

        if (!$shopSystemOrder->hasBeenShipped()) {
            return;
        }

        $this->logger->info('Order: ' . $shopSystemOrder->getOrderNumber() . ' | ReprotShipping started');

        $this->invoiceClient->reportShipping($shopSystemOrder->getShippingReportData());

        $shopSystemOrder->saveHasShippingReported();

        $this->logger->info('Order: ' . $shopSystemOrder->getOrderNumber() . ' | ReprotShipping finished');
    }
}
