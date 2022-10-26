<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionProductNameCalculator
{
    public function calculate(Detail $detail): string
    {
        return $detail->getArticleName();
    }
}
