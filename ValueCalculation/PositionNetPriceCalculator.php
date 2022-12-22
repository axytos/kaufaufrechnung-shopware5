<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionNetPriceCalculator
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPriceCalculator
     */
    private $positionGrossPriceCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxPercentCalculator
     */
    private $positionTaxPercentCalculator;

    public function __construct(PositionGrossPriceCalculator $positionGrossPriceCalculator, PositionTaxPercentCalculator $positionTaxPercentCalculator)
    {
        $this->positionGrossPriceCalculator = $positionGrossPriceCalculator;
        $this->positionTaxPercentCalculator = $positionTaxPercentCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculate($detail)
    {
        $grossPrice = $this->positionGrossPriceCalculator->calculate($detail);
        $taxPercent = $this->positionTaxPercentCalculator->calculate($detail);
        return round($grossPrice / (1 + $taxPercent / 100), 2);
    }
}
