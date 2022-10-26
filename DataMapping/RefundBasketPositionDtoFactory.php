<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketPositionDto;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionProductIdCalculator;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Order;

class RefundBasketPositionDtoFactory
{
    private PositionProductIdCalculator $positionProductIdCalculator;
    private PositionGrossPriceCalculator $positionGrossPriceCalculator;
    private PositionNetPriceCalculator $positionNetPriceCalculator;

    public function __construct(
        PositionProductIdCalculator $positionProductIdCalculator,
        PositionGrossPriceCalculator $positionGrossPriceCalculator,
        PositionNetPriceCalculator $positionNetPriceCalculator
    ) {
        $this->positionProductIdCalculator = $positionProductIdCalculator;
        $this->positionGrossPriceCalculator = $positionGrossPriceCalculator;
        $this->positionNetPriceCalculator = $positionNetPriceCalculator;
    }

    public function create(Detail $orderDetail): RefundBasketPositionDto
    {
        $position = new RefundBasketPositionDto();
        $position->productId = $this->positionProductIdCalculator->calculate($orderDetail);
        $position->grossRefundTotal = $this->positionGrossPriceCalculator->calculate($orderDetail);
        $position->netRefundTotal = $this->positionNetPriceCalculator->calculate($orderDetail);
        return $position;
    }

    public function createShippingPosition(Order $order): RefundBasketPositionDto
    {
        $position = new RefundBasketPositionDto();
        $position->productId = '0';
        $position->grossRefundTotal = $order->getInvoiceShipping();
        $position->netRefundTotal = $order->getInvoiceShippingNet();
        return $position;
    }
}
