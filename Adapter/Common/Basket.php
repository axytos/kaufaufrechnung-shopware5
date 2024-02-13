<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\BasketInterface as CheckoutBasketInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\BasketPositionInterface as CheckoutBasketPositionInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\BasketInterface as InvoiceBasketInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\BasketPositionInterface as InvoiceBasketPositionInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\TaxGroupInterface as InvoiceTaxGroupInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketInterface as UpdateBasketInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketPositionInterface as UpdateBasketPositionInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class Basket implements InvoiceBasketInterface, UpdateBasketInterface, CheckoutBasketInterface
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\BasketPositionFactory
     */
    private $basketPositionFactory;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\TaxGroupFactory
     */
    private $taxGroupFactory;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order
     */
    private $order;

    public function __construct(
        BasketPositionFactory $basketPositionFactory,
        TaxGroupFactory $taxGroupFactory,
        Order $order
    ) {
        $this->basketPositionFactory = $basketPositionFactory;
        $this->taxGroupFactory = $taxGroupFactory;
        $this->order = $order;
    }

    /**
     * @return float
     */
    public function getNetTotal()
    {
        return floatval($this->order->getInvoiceAmountNet());
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->order->getCurrency();
    }

    /**
     * @return float
     */
    public function getGrossTotal()
    {
        return floatval($this->order->getInvoiceAmount());
    }

    /**
     * @return array<InvoiceBasketPositionInterface&UpdateBasketPositionInterface&CheckoutBasketPositionInterface>
     */
    public function getPositions()
    {
        /** @var \Shopware\Models\Order\Detail[] */
        $details = $this->order->getDetails()->getValues();
        $positions = $this->basketPositionFactory->createMany($details);
        $positions[] = $this->basketPositionFactory->createShipping($this->order);
        return $positions;
    }

    /**
     * @return InvoiceTaxGroupInterface[]
     */
    public function getTaxGroups()
    {
        /** @var \Shopware\Models\Order\Detail[] */
        $details = $this->order->getDetails()->getValues();
        $taxGroups = $this->taxGroupFactory->createMany($details);
        $taxGroups[] = $this->taxGroupFactory->createShipping($this->order);
        return $this->taxGroupFactory->combineTaxGroups($taxGroups);
    }
}
