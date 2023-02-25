<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDtoCollection;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Detail as OrderDetail;

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
        /** @var OrderDetail[] */
        $orderDetails = $order->getDetails()->getValues();
        $positions = array_map([$this->shippingBasketPositionDtoFactory, 'create'], $orderDetails);

        array_push($positions, $this->shippingBasketPositionDtoFactory->createShippingPosition());

        return new ShippingBasketPositionDtoCollection(...$positions);
    }
}
