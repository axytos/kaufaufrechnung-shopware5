<?php

namespace AxytosKaufAufRechnungShopware5\CronJob\Commands;

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use AxytosKaufAufRechnungShopware5\CronJob\OrderSyncCommandInterface;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderInterface;

class ReportCreateInvoice implements OrderSyncCommandInterface
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
        if ($shopSystemOrder->hasCreateInvoiceReported()) {
            return;
        }

        if (!$shopSystemOrder->hasBeenInvoiced()) {
            return;
        }

        $this->logger->info('Order: ' . $shopSystemOrder->getOrderNumber() . ' | ReportCreateInvoice started');

        $this->invoiceClient->createInvoice($shopSystemOrder->getCreateInvoiceReportData());

        $shopSystemOrder->saveHasCreateInvoiceReported();

        $this->logger->info('Order: ' . $shopSystemOrder->getOrderNumber() . ' | ReportCreateInvoice finished');
    }
}
