<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDto;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionProductIdCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator;
use Shopware\Models\Order\Detail;

class ShippingBasketPositionDtoFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionProductIdCalculator
     */
    private $positionProductIdCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator
     */
    private $positionQuantityCalculator;

    public function __construct(
        PositionProductIdCalculator $positionProductIdCalculator,
        PositionQuantityCalculator $positionQuantityCalculator
    ) {
        $this->positionProductIdCalculator = $positionProductIdCalculator;
        $this->positionQuantityCalculator = $positionQuantityCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $shippingItem
     * @return \Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDto
     */
    public function create($shippingItem)
    {
        $position = new ShippingBasketPositionDto();
        $position->productId = $this->positionProductIdCalculator->calculate($shippingItem);
        $position->quantity = $this->positionQuantityCalculator->calculate($shippingItem);
        return $position;
    }

    /**
     * @return \Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDto
     */
    public function createShippingPosition()
    {
        $position = new ShippingBasketPositionDto();
        $position->productId = '0';
        $position->quantity = 1;
        return $position;
    }
}
