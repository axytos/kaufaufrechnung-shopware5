<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionTaxPercentCalculator
{
    public function calculate(Detail $detail): float
    {
        return $detail->getTaxRate();
    }
}
