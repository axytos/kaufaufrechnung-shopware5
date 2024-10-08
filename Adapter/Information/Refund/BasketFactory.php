<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information\Refund;

use AxytosKaufAufRechnungShopware5\Adapter\Common\TaxGroupFactory;

class BasketFactory
{
    /**
     * @var BasketPositionFactory
     */
    private $basketPositionFactory;

    /**
     * @var TaxGroupFactory
     */
    private $taxGroupFactory;

    public function __construct(
        BasketPositionFactory $basketPositionFactory,
        TaxGroupFactory $taxGroupFactory
    ) {
        $this->basketPositionFactory = $basketPositionFactory;
        $this->taxGroupFactory = $taxGroupFactory;
    }

    /**
     * @param \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order $order
     *
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\BasketInterface
     */
    public function create($order)
    {
        return new Basket(
            $this->basketPositionFactory,
            $this->taxGroupFactory,
            $order
        );
    }
}
