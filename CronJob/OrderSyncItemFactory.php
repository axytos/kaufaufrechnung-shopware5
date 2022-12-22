<?php

namespace AxytosKaufAufRechnungShopware5\CronJob;

use Axytos\ECommerce\Clients\ErrorReporting\ErrorReportingClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;

class OrderSyncItemFactory
{
    /**
     * @var \Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface
     */
    private $invoiceClient;

    /**
     * @var \Axytos\ECommerce\Clients\ErrorReporting\ErrorReportingClientInterface
     */
    private $errorReportingClient;

    /**
     * @var \Axytos\ECommerce\Logging\LoggerAdapterInterface
     */
    private $logger;

    public function __construct(
        InvoiceClientInterface $invoiceClient,
        ErrorReportingClientInterface $errorReportingClient,
        LoggerAdapterInterface $logger
    ) {
        $this->invoiceClient = $invoiceClient;
        $this->errorReportingClient = $errorReportingClient;
        $this->logger = $logger;
    }

    /**
     * @param ShopSystemOrderInterface $shopSystemOrder
     * @return OrderSyncItemInterface
     */
    public function create($shopSystemOrder)
    {
        return new OrderSyncItem(
            $shopSystemOrder,
            $this->invoiceClient,
            $this->errorReportingClient,
            $this->logger
        );
    }

    /**
     * @param ShopSystemOrderInterface[] $shopSystemOrders
     * @return OrderSyncItemInterface[]
     */
    public function createMany($shopSystemOrders)
    {
        return array_map([$this,'create'], $shopSystemOrders);
    }
}
