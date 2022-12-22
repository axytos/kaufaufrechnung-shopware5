<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionQuantityCalculator
{
    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return int
     */
    public function calculate($detail)
    {
        return $detail->getQuantity();
    }
}
