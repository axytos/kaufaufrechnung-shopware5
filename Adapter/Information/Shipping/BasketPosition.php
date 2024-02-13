<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information\Shipping;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Shipping\BasketPositionInterface;
use Shopware\Models\Order\Detail;

class BasketPosition implements BasketPositionInterface
{
    /**
     * @var \Shopware\Models\Order\Detail
     */
    private $orderDetail;

    public function __construct(Detail $orderDetail)
    {
        $this->orderDetail = $orderDetail;
    }

    /**
     * @return string
     */
    public function getProductNumber()
    {
        return $this->orderDetail->getArticleNumber();
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->orderDetail->getQuantity();
    }
}
