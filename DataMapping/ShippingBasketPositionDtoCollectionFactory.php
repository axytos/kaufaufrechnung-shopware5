<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDtoCollection;
use Shopware\Models\Order\Order;

class ShippingBasketPositionDtoCollectionFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\ShippingBasketPositionDtoFactory
     */
    private $shippingBasketPositionDtoFactory;

    public function __construct(ShippingBasketPositionDtoFactory $shippingBasketPositionDtoFactory)
    {
        $this->shippingBasketPositionDtoFactory = $shippingBasketPositionDtoFactory;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDtoCollection
     */
    public function create($order)
    {
        $positions = array_map([$this->shippingBasketPositionDtoFactory, 'create'], $order->getDetails()->getValues());

        array_push($positions, $this->shippingBasketPositionDtoFactory->createShippingPosition());

        return new ShippingBasketPositionDtoCollection(...$positions);
    }
}
