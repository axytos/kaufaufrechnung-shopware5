<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketInterface as UpdateBasketInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\BasketInterface as CheckoutBasketInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\BasketInterface as InvoiceBasketInterface;

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
     * @param UnifiedShopwareModel\Order $order
     *
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
