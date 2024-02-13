<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\BasketInterface as CheckoutBasketInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\BasketInterface as InvoiceBasketInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketInterface as UpdateBasketInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\TaxGroupFactory;

class BasketFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\BasketPositionFactory
     */
    private $basketPositionFactory;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\TaxGroupFactory
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
     * @return InvoiceBasketInterface&UpdateBasketInterface&CheckoutBasketInterface
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
