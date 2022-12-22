<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketPositionDto;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionProductIdCalculator;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Order;

class RefundBasketPositionDtoFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionProductIdCalculator
     */
    private $positionProductIdCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPriceCalculator
     */
    private $positionGrossPriceCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator
     */
    private $positionNetPriceCalculator;

    public function __construct(
        PositionProductIdCalculator $positionProductIdCalculator,
        PositionGrossPriceCalculator $positionGrossPriceCalculator,
        PositionNetPriceCalculator $positionNetPriceCalculator
    ) {
        $this->positionProductIdCalculator = $positionProductIdCalculator;
        $this->positionGrossPriceCalculator = $positionGrossPriceCalculator;
        $this->positionNetPriceCalculator = $positionNetPriceCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $orderDetail
     * @return \Axytos\ECommerce\DataTransferObjects\RefundBasketPositionDto
     */
    public function create($orderDetail)
    {
        $position = new RefundBasketPositionDto();
        $position->productId = $this->positionProductIdCalculator->calculate($orderDetail);
        $position->grossRefundTotal = $this->positionGrossPriceCalculator->calculate($orderDetail);
        $position->netRefundTotal = $this->positionNetPriceCalculator->calculate($orderDetail);
        return $position;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\DataTransferObjects\RefundBasketPositionDto
     */
    public function createShippingPosition($order)
    {
        $position = new RefundBasketPositionDto();
        $position->productId = '0';
        $position->grossRefundTotal = $order->getInvoiceShipping();
        $position->netRefundTotal = $order->getInvoiceShippingNet();
        return $position;
    }
}
