<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\BasketPositionInterface as CheckoutBasketPositionInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Invoice\BasketPositionInterface as InvoiceBasketPositionInterface;
use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\BasketUpdate\BasketPositionInterface as UpdateBasketPositionInterface;
use Shopware\Models\Order\Detail;

class BasketPosition implements InvoiceBasketPositionInterface, UpdateBasketPositionInterface, CheckoutBasketPositionInterface
{
    use BasketPositionCalculationTrait;

    /**
     * @var \Shopware\Models\Order\Detail
     */
    private $invoiceItem;

    public function __construct(
        Detail $invoiceItem
    ) {
        $this->invoiceItem = $invoiceItem;
    }

    /**
     * @return string
     */
    public function getProductNumber()
    {
        return $this->invoiceItem->getArticleNumber();
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->invoiceItem->getArticleName();
    }

    /**
     * @return string|null
     */
    public function getProductCategory()
    {
        return null;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->invoiceItem->getQuantity();
    }

    /**
     * @return float
     */
    public function getTaxPercent()
    {
        return $this->invoiceItem->getTaxRate();
    }

    /**
     * @return float
     */
    public function getNetPricePerUnit()
    {
        return $this->calculateNetPricePerUnit($this->invoiceItem);
    }

    /**
     * @return float
     */
    public function getGrossPricePerUnit()
    {
        return $this->invoiceItem->getPrice();
    }

    /**
     * @return float
     */
    public function getNetPositionTotal()
    {
        return $this->calculateNetPrice($this->invoiceItem);
    }

    /**
     * @return float
     */
    public function getGrossPositionTotal()
    {
        return $this->calculateGrossPrice($this->invoiceItem);
    }
}
