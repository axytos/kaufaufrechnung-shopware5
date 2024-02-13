<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\RefundInformationInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class RefundInformation implements RefundInformationInterface
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order
     */
    private $order;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketFactory
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

    /**
     * @return string|int
     */
    public function getInvoiceNumber()
    {
        $invoiceDocument = $this->order->findInvoiceDocument();
        if (!is_null($invoiceDocument)) {
            return $invoiceDocument->getDocumentId();
        }
        return '';
    }

    /**
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\BasketInterface
     */
    public function getBasket()
    {
        $creditDocument = $this->order->findCreditDocument();
        if (!is_null($creditDocument)) {
            return $this->basketFactory->create($creditDocument->getOrder());
        }

        return $this->basketFactory->create($this->order);
    }
}
