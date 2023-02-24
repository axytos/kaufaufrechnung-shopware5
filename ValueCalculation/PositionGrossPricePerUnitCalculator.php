<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

class PositionGrossPricePerUnitCalculator
{
    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculate($detail)
    {
        return $detail->getPrice();
    }
}
