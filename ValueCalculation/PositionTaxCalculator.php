<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionTaxCalculator
{
    private PositionGrossPriceCalculator $positionGrossPriceCalculator;
    private PositionNetPriceCalculator $positionNetPriceCalculator;

    public function __construct(
        PositionGrossPriceCalculator $positionGrossPriceCalculator,
        PositionNetPriceCalculator $positionNetPriceCalculator
    ) {
        $this->positionGrossPriceCalculator = $positionGrossPriceCalculator;
        $this->positionNetPriceCalculator = $positionNetPriceCalculator;
    }

    public function calculate(Detail $detail): float
    {
        $grossPrice = $this->positionGrossPriceCalculator->calculate($detail);
        $netPrice = $this->positionNetPriceCalculator->calculate($detail);
        return floatval($grossPrice - $netPrice);
    }
}
