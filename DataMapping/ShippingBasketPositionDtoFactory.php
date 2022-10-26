<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDto;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionProductIdCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator;
use Shopware\Models\Order\Detail;

class ShippingBasketPositionDtoFactory
{
    private PositionProductIdCalculator $positionProductIdCalculator;
    private PositionQuantityCalculator $positionQuantityCalculator;

    public function __construct(
        PositionProductIdCalculator $positionProductIdCalculator,
        PositionQuantityCalculator $positionQuantityCalculator
    ) {
        $this->positionProductIdCalculator = $positionProductIdCalculator;
        $this->positionQuantityCalculator = $positionQuantityCalculator;
    }

    public function create(Detail $shippingItem): ShippingBasketPositionDto
    {
        $position = new ShippingBasketPositionDto();
        $position->productId = $this->positionProductIdCalculator->calculate($shippingItem);
        $position->quantity = $this->positionQuantityCalculator->calculate($shippingItem);
        return $position;
    }

    public function createShippingPosition(): ShippingBasketPositionDto
    {
        $position = new ShippingBasketPositionDto();
        $position->productId = '0';
        $position->quantity = 1;
        return $position;
    }
}
