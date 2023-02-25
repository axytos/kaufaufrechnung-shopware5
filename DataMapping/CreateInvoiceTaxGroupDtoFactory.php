<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\CreateInvoiceTaxGroupDto;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxPercentCalculator;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Order;

class CreateInvoiceTaxGroupDtoFactory
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
     * @param \Shopware\Models\Order\Detail $invoiceItem
     * @return \Axytos\ECommerce\DataTransferObjects\CreateInvoiceTaxGroupDto
     */
    public function create($invoiceItem)
    {
        $taxGroup = new CreateInvoiceTaxGroupDto();
        $taxGroup->valueToTax = $this->positionNetPriceCalculator->calculate($invoiceItem);
        $taxGroup->total = $this->positionTaxCalculator->calculate($invoiceItem);
        $taxGroup->taxPercent = $this->positionTaxPercentCalculator->calculate($invoiceItem);

        return $taxGroup;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\DataTransferObjects\CreateInvoiceTaxGroupDto
     */
    public function createShippingTaxGroup($order)
    {
        $taxGroup = new CreateInvoiceTaxGroupDto();
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
