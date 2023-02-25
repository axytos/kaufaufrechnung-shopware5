<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\RefundBasketTaxGroupDto;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxPercentCalculator;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Order;

class RefundBasketTaxGroupDtoFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator
     */
    private $positionNetPriceCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxCalculator
     */
    private $positionTaxCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxPercentCalculator
     */
    private $positionTaxPercentCalculator;

    public function __construct(
        PositionNetPriceCalculator $positionNetPriceCalculator,
        PositionTaxCalculator $positionTaxCalculator,
        PositionTaxPercentCalculator $positionTaxPercentCalculator
    ) {
        $this->positionNetPriceCalculator = $positionNetPriceCalculator;
        $this->positionTaxCalculator = $positionTaxCalculator;
        $this->positionTaxPercentCalculator = $positionTaxPercentCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $orderDetail
     * @return \Axytos\ECommerce\DataTransferObjects\RefundBasketTaxGroupDto
     */
    public function create($orderDetail)
    {
        $taxGroup = new RefundBasketTaxGroupDto();
        $taxGroup->valueToTax = $this->positionNetPriceCalculator->calculate($orderDetail);
        $taxGroup->total = $this->positionTaxCalculator->calculate($orderDetail);
        $taxGroup->taxPercent = $this->positionTaxPercentCalculator->calculate($orderDetail);
        return $taxGroup;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\DataTransferObjects\RefundBasketTaxGroupDto
     */
    public function createShippingTaxGroup($order)
    {
        $taxGroup = new RefundBasketTaxGroupDto();
        $taxGroup->valueToTax = $order->getInvoiceShippingNet();
        $taxGroup->total = floatval($order->getInvoiceShipping() - $order->getInvoiceShippingNet());
        $taxGroup->taxPercent = $this->getShippingTaxPercent($order);
        return $taxGroup;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return float|null
     */
    private function getShippingTaxPercent($order)
    {
        // shopware 5.3 compatibility
        if (method_exists($order, 'getInvoiceShippingTaxRate')) {
            return $order->getInvoiceShippingTaxRate();
        }

        $grossTotal = $order->getInvoiceShipping();
        $netTotal = $order->getInvoiceShippingNet();
        return (1 - ($netTotal / $grossTotal)) * 100;
    }
}
