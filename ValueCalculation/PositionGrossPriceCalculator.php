<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

class PositionGrossPriceCalculator
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPricePerUnitCalculator
     */
    private $positionGrossPricePerUnitCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator
     */
    private $positionQuantityCalculator;

    public function __construct(
        PositionGrossPricePerUnitCalculator $positionGrossPricePerUnitCalculator,
        PositionQuantityCalculator $positionQuantityCalculator
    ) {
        $this->positionGrossPricePerUnitCalculator = $positionGrossPricePerUnitCalculator;
        $this->positionQuantityCalculator = $positionQuantityCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculate($detail)
    {
        $grossPricePerUnit = $this->positionGrossPricePerUnitCalculator->calculate($detail);
        $quantity = $this->positionQuantityCalculator->calculate($detail);
        return round($grossPricePerUnit * $quantity, 2);
    }
}
