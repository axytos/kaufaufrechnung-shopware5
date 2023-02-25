<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\BasketPositionDtoCollection;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Detail as OrderDetail;

class BasketPositionDtoCollectionFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\BasketPositionDtoFactory
     */
    private $basketPositionDtoFactory;

    public function __construct(BasketPositionDtoFactory $basketPositionDtoFactory)
    {
        $this->basketPositionDtoFactory = $basketPositionDtoFactory;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\DataTransferObjects\BasketPositionDtoCollection
     */
    public function create($order)
    {
        $details = $order->getDetails();

        $positions = [];

        /** @var OrderDetail $detail */
        foreach ($details as $detail) {
            $positions[] = $this->basketPositionDtoFactory->create($detail);
        }

        $positions[] = $this->basketPositionDtoFactory->createShippingPosition($order);

        return new BasketPositionDtoCollection(...$positions);
    }
}
