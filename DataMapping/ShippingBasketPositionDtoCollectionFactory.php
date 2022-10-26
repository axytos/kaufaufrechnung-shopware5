<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDtoCollection;
use Shopware\Models\Order\Order;

class ShippingBasketPositionDtoCollectionFactory
{
    private ShippingBasketPositionDtoFactory $shippingBasketPositionDtoFactory;

    public function __construct(ShippingBasketPositionDtoFactory $shippingBasketPositionDtoFactory)
    {
        $this->shippingBasketPositionDtoFactory = $shippingBasketPositionDtoFactory;
    }

    public function create(Order $order): ShippingBasketPositionDtoCollection
    {
        $positions = array_map([$this->shippingBasketPositionDtoFactory, 'create'], $order->getDetails()->getValues());

        array_push($positions, $this->shippingBasketPositionDtoFactory->createShippingPosition());

        return new ShippingBasketPositionDtoCollection(...$positions);
    }
}
