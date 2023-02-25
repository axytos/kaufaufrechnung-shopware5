<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\ArticleDetailRepository;
use Shopware\Models\Order\Detail as OrderDetail;
use Shopware\Models\Order\Order;

class DeliveryWeightCalculator
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\ArticleDetailRepository
     */
    private $articleDetailRepository;

    public function __construct(ArticleDetailRepository $articleDetailRepository)
    {
        $this->articleDetailRepository = $articleDetailRepository;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return float
     */
    public function calculate($order)
    {
        /** @var float */
        $deliveryWeight = 0;

        /** @var OrderDetail $orderDetail */
        foreach ($order->getDetails() as $orderDetail) {
            $deliveryWeight += $this->calculateOrderDetailWeight($orderDetail);
        }

        return $deliveryWeight;
    }

    /**
     * @return float
     */
    private function calculateOrderDetailWeight(OrderDetail $orderDetail)
    {
        $quantity = $orderDetail->getQuantity();
        $articleWeight = $this->articleDetailRepository->findArticleWeightForOrderDetail($orderDetail);

        return $quantity * floatval($articleWeight);
    }
}
