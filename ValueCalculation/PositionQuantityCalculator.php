<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionQuantityCalculator
{
    public function calculate(Detail $detail): int
    {
        return $detail->getQuantity();
    }
}
