<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketPositionDtoCollection;
use Shopware\Models\Order\Document\Document;

class RefundBasketPositionDtoCollectionFactory
{
    private RefundBasketPositionDtoFactory $positionFactory;

    public function __construct(RefundBasketPositionDtoFactory $positionFactory)
    {
        $this->positionFactory = $positionFactory;
    }

    public function create(Document $creditDocument): RefundBasketPositionDtoCollection
    {
        $order = $creditDocument->getOrder();
        $details = $order->getDetails();

        $positions = array_map([$this->positionFactory, 'create'], $details->toArray());

        $positions[] = $this->positionFactory->createShippingPosition($order);

        return new RefundBasketPositionDtoCollection(...$positions);
    }
}
