<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common;

trait BasketPositionCalculationTrait
{
    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculateGrossPrice($detail)
    {
        $grossPricePerUnit = floatval($detail->getPrice());
        $quantity = $detail->getQuantity();
        return round($grossPricePerUnit * $quantity, 2);
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculateNetPrice($detail)
    {
        $netPricePeUnit = $this->calculateNetPricePerUnit($detail);
        $quantity = $detail->getQuantity();
        return round($netPricePeUnit * $quantity, 2);
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculateNetPricePerUnit($detail)
    {
        $grossPrice = floatval($detail->getPrice());
        $taxPercent = floatval($detail->getTaxRate());
        return round($grossPrice / (1 + $taxPercent / 100), 2);
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculateTax($detail)
    {
        $grossPrice = $this->calculateGrossPrice($detail);
        $netPrice = $this->calculateNetPrice($detail);
        return floatval($grossPrice - $netPrice);
    }
}
