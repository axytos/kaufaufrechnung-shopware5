<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\BasketPositionDtoCollection;
use Shopware\Models\Order\Order;

class BasketPositionDtoCollectionFactory
{
    private BasketPositionDtoFactory $basketPositionDtoFactory;

    public function __construct(BasketPositionDtoFactory $basketPositionDtoFactory)
    {
        $this->basketPositionDtoFactory = $basketPositionDtoFactory;
    }

    public function create(Order $order): BasketPositionDtoCollection
    {
        $details = $order->getDetails();

        $positions = [];

        foreach ($details as $detail) {
            $positions[] = $this->basketPositionDtoFactory->create($detail);
        }

        $positions[] = $this->basketPositionDtoFactory->createShippingPosition($order);

        return new BasketPositionDtoCollection(...$positions);
    }
}
