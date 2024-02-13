<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information\Refund;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\BasketInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\TaxGroupFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class Basket implements BasketInterface
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Information\Refund\BasketPositionFactory
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
     * @return float
     */
    public function getGrossTotal()
    {
        return floatval($this->order->getInvoiceAmount());
    }

    public function getPositions()
    {
        /** @var \Shopware\Models\Order\Detail[] */
        $details = $this->order->getDetails()->getValues();
        $positions = $this->basketPositionFactory->createMany($details);
        $positions[] = $this->basketPositionFactory->createShipping($this->order);
        return $positions;
    }

    public function getTaxGroups()
    {
        /** @var \Shopware\Models\Order\Detail[] */
        $details = $this->order->getDetails()->getValues();
        $taxGroups = $this->taxGroupFactory->createMany($details);
        $taxGroups[] = $this->taxGroupFactory->createShipping($this->order);
        return $this->taxGroupFactory->combineTaxGroups($taxGroups);
    }
}
