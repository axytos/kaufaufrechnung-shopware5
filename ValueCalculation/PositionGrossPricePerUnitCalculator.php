<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionGrossPricePerUnitCalculator
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPriceCalculator
     */
    private $positionGrossPriceCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator
     */
    private $positionQuantityCalculator;

    public function __construct(PositionGrossPriceCalculator $positionGrossPriceCalculator, PositionQuantityCalculator $positionQuantityCalculator)
    {
        $this->positionGrossPriceCalculator = $positionGrossPriceCalculator;
        $this->positionQuantityCalculator = $positionQuantityCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculate($detail)
    {
        $grossPrice = $this->positionGrossPriceCalculator->calculate($detail);
        $quantity = $this->positionQuantityCalculator->calculate($detail);
        return round($grossPrice / $quantity, 2);
    }
}
