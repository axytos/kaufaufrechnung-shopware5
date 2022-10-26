<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionNetPricePerUnitCalculator
{
    private PositionNetPriceCalculator $positionNetPriceCalculator;
    private PositionQuantityCalculator $positionQuantityCalculator;

    public function __construct(PositionNetPriceCalculator $positionNetPriceCalculator, PositionQuantityCalculator $positionQuantityCalculator)
    {
        $this->positionNetPriceCalculator = $positionNetPriceCalculator;
        $this->positionQuantityCalculator = $positionQuantityCalculator;
    }

    public function calculate(Detail $detail): float
    {
        $netPrice = $this->positionNetPriceCalculator->calculate($detail);
        $quantity = $this->positionQuantityCalculator->calculate($detail);
        return round($netPrice / $quantity, 2);
    }
}
