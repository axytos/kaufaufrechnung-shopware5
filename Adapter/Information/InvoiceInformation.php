<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\InvoiceInformationInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class InvoiceInformation implements InvoiceInformationInterface
{
    /**
     * @var Order
     */
    private $order;

    /**
     * @var BasketFactory
     */
    private $basketFactory;

    public function __construct(
        Order $order,
        BasketFactory $basketFactory
    ) {
        $this->order = $order;
        $this->basketFactory = $basketFactory;
    }

    /**
     * @return string|int
     */
    public function getOrderNumber()
    {
        return $this->order->getNumber();
    }

    public function getInvoiceNumber()
    {
        $invoice = $this->order->findInvoiceDocument();
        if (!is_null($invoice)) {
            return $invoice->getDocumentId();
        }

        return '';
    }

    public function getBasket()
    {
        return $this->basketFactory->create($this->order);
    }
}
