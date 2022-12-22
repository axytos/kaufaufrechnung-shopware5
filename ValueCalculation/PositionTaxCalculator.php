<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Detail;

class PositionTaxCalculator
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPriceCalculator
     */
    private $positionGrossPriceCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator
     */
    private $positionNetPriceCalculator;

    public function __construct(
        PositionGrossPriceCalculator $positionGrossPriceCalculator,
        PositionNetPriceCalculator $positionNetPriceCalculator
    ) {
        $this->positionGrossPriceCalculator = $positionGrossPriceCalculator;
        $this->positionNetPriceCalculator = $positionNetPriceCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $detail
     * @return float
     */
    public function calculate($detail)
    {
        $grossPrice = $this->positionGrossPriceCalculator->calculate($detail);
        $netPrice = $this->positionNetPriceCalculator->calculate($detail);
        return floatval($grossPrice - $netPrice);
    }
}
