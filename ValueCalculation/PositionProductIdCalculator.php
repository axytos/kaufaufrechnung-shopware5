<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionProductIdCalculator
{
    public function calculate(Detail $detail): string
    {
        return $detail->getArticleNumber();
    }
}
