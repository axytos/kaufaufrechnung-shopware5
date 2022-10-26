<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketTaxGroupDto;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxPercentCalculator;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Order;

class RefundBasketTaxGroupDtoFactory
{
    private PositionNetPriceCalculator $positionNetPriceCalculator;
    private PositionTaxCalculator $positionTaxCalculator;
    private PositionTaxPercentCalculator $positionTaxPercentCalculator;

    public function __construct(
        PositionNetPriceCalculator $positionNetPriceCalculator,
        PositionTaxCalculator $positionTaxCalculator,
        PositionTaxPercentCalculator $positionTaxPercentCalculator
    ) {
        $this->positionNetPriceCalculator = $positionNetPriceCalculator;
        $this->positionTaxCalculator = $positionTaxCalculator;
        $this->positionTaxPercentCalculator = $positionTaxPercentCalculator;
    }

    public function create(Detail $orderDetail): RefundBasketTaxGroupDto
    {
        $taxGroup = new RefundBasketTaxGroupDto();
        $taxGroup->valueToTax = $this->positionNetPriceCalculator->calculate($orderDetail);
        $taxGroup->total = $this->positionTaxCalculator->calculate($orderDetail);
        $taxGroup->taxPercent = $this->positionTaxPercentCalculator->calculate($orderDetail);
        return $taxGroup;
    }

    public function createShippingTaxGroup(Order $order): RefundBasketTaxGroupDto
    {
        $taxGroup = new RefundBasketTaxGroupDto();
        $taxGroup->valueToTax = $order->getInvoiceShippingNet();
        $taxGroup->total = floatval($order->getInvoiceShipping() - $order->getInvoiceShippingNet());
        $taxGroup->taxPercent = $order->getInvoiceShippingTaxRate();
        return $taxGroup;
    }
}
