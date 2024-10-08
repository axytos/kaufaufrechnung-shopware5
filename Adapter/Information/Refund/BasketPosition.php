<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information\Refund;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Refund\BasketPositionInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketPositionCalculationTrait;
use Shopware\Models\Order\Detail;

class BasketPosition implements BasketPositionInterface
{
    use BasketPositionCalculationTrait;

    /**
     * @var Detail
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
     * @return float
     */
    public function getNetRefundTotal()
    {
        return $this->calculateNetPrice($this->invoiceItem);
    }

    /**
     * @return float
     */
    public function getGrossRefundTotal()
    {
        return $this->calculateGrossPrice($this->invoiceItem);
    }
}
