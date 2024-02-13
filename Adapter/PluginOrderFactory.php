<?php

namespace AxytosKaufAufRechnungShopware5\Adapter;

use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation\HashCalculator;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketFactory as RefundBasketFactory;

class PluginOrderFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory
     */
    private $basketFactory;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketFactory
     */
    private $refundBasketFactory;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation\HashCalculator
     */
    private $hashCalculator;

    public function __construct(
        BasketFactory $basketFactory,
        RefundBasketFactory $refundBasketFactory,
        HashCalculator $hashCalculator
    ) {
        $this->basketFactory = $basketFactory;
        $this->refundBasketFactory = $refundBasketFactory;
        $this->hashCalculator = $hashCalculator;
    }

    /**
     * @param \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order $unifiedShopwareOrder
     * @return \AxytosKaufAufRechnungShopware5\Adapter\PluginOrder
     */
    public function create($unifiedShopwareOrder)
    {
        return new PluginOrder(
            $unifiedShopwareOrder,
            $this->basketFactory,
            $this->refundBasketFactory,
            $this->hashCalculator
        );
    }

    /**
     * @param \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order[] $orders
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\PluginOrderInterface[]
     */
    public function createMany($orders)
    {
        return array_map([$this, 'create'], $orders);
    }
}
