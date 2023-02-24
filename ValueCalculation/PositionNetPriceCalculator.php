<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

class PositionNetPriceCalculator
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPricePerUnitCalculator
     */
    private $positionNetPricePerUnitCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator
     */
    private $positionQuantityCalculator;

    public function __construct(
        PositionNetPricePerUnitCalculator $positionNetPricePerUnitCalculator,
        PositionQuantityCalculator $positionQuantityCalculator
    ) {
        $this->positionNetPricePerUnitCalculator = $positionNetPricePerUnitCalculator;
        $this->positionQuantityCalculator = $positionQuantityCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculate($detail)
    {
        $netPrice = $this->positionNetPricePerUnitCalculator->calculate($detail);
        $quantity = $this->positionQuantityCalculator->calculate($detail);
        return round($netPrice * $quantity, 2);
    }
}
