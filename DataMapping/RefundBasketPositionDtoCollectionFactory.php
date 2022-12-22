<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketPositionDtoCollection;
use Shopware\Models\Order\Document\Document;

class RefundBasketPositionDtoCollectionFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\RefundBasketPositionDtoFactory
     */
    private $positionFactory;

    public function __construct(RefundBasketPositionDtoFactory $positionFactory)
    {
        $this->positionFactory = $positionFactory;
    }

    /**
     * @param \Shopware\Models\Order\Document\Document $creditDocument
     * @return \Axytos\ECommerce\DataTransferObjects\RefundBasketPositionDtoCollection
     */
    public function create($creditDocument)
    {
        $order = $creditDocument->getOrder();
        $details = $order->getDetails();

        $positions = array_map([$this->positionFactory, 'create'], $details->toArray());

        $positions[] = $this->positionFactory->createShippingPosition($order);

        return new RefundBasketPositionDtoCollection(...$positions);
    }
}
