<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionGrossPriceCalculator
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
