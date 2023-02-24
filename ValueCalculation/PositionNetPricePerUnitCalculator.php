<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

class PositionNetPricePerUnitCalculator
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPricePerUnitCalculator
     */
    private $positionGrossPricePerUnitCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxPercentCalculator
     */
    private $positionTaxPercentCalculator;

    public function __construct(
        PositionGrossPricePerUnitCalculator $positionGrossPricePerUnitCalculator,
        PositionTaxPercentCalculator $positionTaxPercentCalculator
    ) {
        $this->positionGrossPricePerUnitCalculator = $positionGrossPricePerUnitCalculator;
        $this->positionTaxPercentCalculator = $positionTaxPercentCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculate($detail)
    {
        $grossPrice = $this->positionGrossPricePerUnitCalculator->calculate($detail);
        $taxPercent = $this->positionTaxPercentCalculator->calculate($detail);
        return round($grossPrice / (1 + $taxPercent / 100), 2);
    }
}
