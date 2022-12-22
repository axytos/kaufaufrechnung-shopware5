<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionNetPricePerUnitCalculator
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator
     */
    private $positionNetPriceCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator
     */
    private $positionQuantityCalculator;

    public function __construct(PositionNetPriceCalculator $positionNetPriceCalculator, PositionQuantityCalculator $positionQuantityCalculator)
    {
        $this->positionNetPriceCalculator = $positionNetPriceCalculator;
        $this->positionQuantityCalculator = $positionQuantityCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculate($detail)
    {
        $netPrice = $this->positionNetPriceCalculator->calculate($detail);
        $quantity = $this->positionQuantityCalculator->calculate($detail);
        return round($netPrice / $quantity, 2);
    }
}
