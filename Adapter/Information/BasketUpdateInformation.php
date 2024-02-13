<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdateInformationInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class BasketUpdateInformation implements BasketUpdateInformationInterface
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order
     */
    private $order;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory
     */
    private $basketFactory;

    public function __construct(Order $order, BasketFactory $basketFactory)
    {
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
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketInterface
     */
    public function getBasket()
    {
        return $this->basketFactory->create($this->order);
    }
}
