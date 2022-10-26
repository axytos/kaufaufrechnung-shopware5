<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionNetPriceCalculator
{
    private PositionGrossPriceCalculator $positionGrossPriceCalculator;
    private PositionTaxPercentCalculator $positionTaxPercentCalculator;

    public function __construct(PositionGrossPriceCalculator $positionGrossPriceCalculator, PositionTaxPercentCalculator $positionTaxPercentCalculator)
    {
        $this->positionGrossPriceCalculator = $positionGrossPriceCalculator;
        $this->positionTaxPercentCalculator = $positionTaxPercentCalculator;
    }

    public function calculate(Detail $detail): float
    {
        $grossPrice = $this->positionGrossPriceCalculator->calculate($detail);
        $taxPercent = $this->positionTaxPercentCalculator->calculate($detail);
        return round($grossPrice / (1 + $taxPercent / 100), 2);
    }
}
