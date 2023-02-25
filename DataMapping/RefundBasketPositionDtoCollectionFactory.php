<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketPositionDtoCollection;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Detail as OrderDetail;

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
        /** @var OrderDetail[] */
        $details = $order->getDetails()->toArray();

        $positions = array_map([$this->positionFactory, 'create'], $details);

        $positions[] = $this->positionFactory->createShippingPosition($order);

        return new RefundBasketPositionDtoCollection(...$positions);
    }
}
