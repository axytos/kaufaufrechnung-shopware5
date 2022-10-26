<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionGrossPricePerUnitCalculator
{
    private PositionGrossPriceCalculator $positionGrossPriceCalculator;
    private PositionQuantityCalculator $positionQuantityCalculator;

    public function __construct(PositionGrossPriceCalculator $positionGrossPriceCalculator, PositionQuantityCalculator $positionQuantityCalculator)
    {
        $this->positionGrossPriceCalculator = $positionGrossPriceCalculator;
        $this->positionQuantityCalculator = $positionQuantityCalculator;
    }

    public function calculate(Detail $detail): float
    {
        $grossPrice = $this->positionGrossPriceCalculator->calculate($detail);
        $quantity = $this->positionQuantityCalculator->calculate($detail);
        return round($grossPrice / $quantity, 2);
    }
}
