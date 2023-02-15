<?php

namespace AxytosKaufAufRechnungShopware5\CronJob;

use AxytosKaufAufRechnungShopware5\Core\InvoiceOrderContextFactory;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository;

class ShopSystemOrderFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderRepository
     */
    private $orderRepository;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Core\InvoiceOrderContextFactory
     */
    private $invoiceOrderContextFactory;

    public function __construct(
        OrderRepository $orderRepository,
        InvoiceOrderContextFactory $invoiceOrderContextFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceOrderContextFactory = $invoiceOrderContextFactory;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\OrderSync\ShopSystemOrderInterface
     */
    public function create($order)
    {
        return new ShopSystemOrder(
            $order,
            $this->orderRepository,
            $this->invoiceOrderContextFactory
        );
    }

    /**
     * @param \Shopware\Models\Order\Order[] $orders
     * @return \Axytos\ECommerce\OrderSync\ShopSystemOrderInterface[]
     */
    public function createMany($orders)
    {
        return array_map([$this, 'create'], $orders);
    }
}
