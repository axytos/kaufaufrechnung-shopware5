<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionProductIdCalculator
{
    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return string
     */
    public function calculate($detail)
    {
        return $detail->getArticleNumber();
    }
}
