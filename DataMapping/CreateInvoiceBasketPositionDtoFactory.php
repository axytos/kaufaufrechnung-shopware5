<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketPositionDto;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPricePerUnitCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPricePerUnitCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionProductIdCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionProductNameCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator;
use AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxPercentCalculator;
use Shopware\Models\Order\Detail;
use Shopware\Models\Order\Order;

class CreateInvoiceBasketPositionDtoFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionProductIdCalculator
     */
    private $positionProductIdCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionProductNameCalculator
     */
    private $positionProductNameCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionQuantityCalculator
     */
    private $positionQuantityCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionTaxPercentCalculator
     */
    private $positionTaxPercentCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPriceCalculator
     */
    private $positionGrossPriceCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionGrossPricePerUnitCalculator
     */
    private $positionGrossPricePerUnitCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPriceCalculator
     */
    private $positionNetPriceCalculator;
    /**
     * @var \AxytosKaufAufRechnungShopware5\ValueCalculation\PositionNetPricePerUnitCalculator
     */
    private $positionNetPricePerUnitCalculator;

    public function __construct(
        PositionProductIdCalculator $positionProductIdCalculator,
        PositionProductNameCalculator $positionProductNameCalculator,
        PositionQuantityCalculator $positionQuantityCalculator,
        PositionTaxPercentCalculator $positionTaxPercentCalculator,
        PositionGrossPriceCalculator $positionGrossPriceCalculator,
        PositionGrossPricePerUnitCalculator $positionGrossPricePerUnitCalculator,
        PositionNetPriceCalculator $positionNetPriceCalculator,
        PositionNetPricePerUnitCalculator $positionNetPricePerUnitCalculator
    ) {
        $this->positionProductIdCalculator = $positionProductIdCalculator;
        $this->positionProductNameCalculator = $positionProductNameCalculator;
        $this->positionQuantityCalculator = $positionQuantityCalculator;
        $this->positionTaxPercentCalculator = $positionTaxPercentCalculator;
        $this->positionGrossPriceCalculator = $positionGrossPriceCalculator;
        $this->positionGrossPricePerUnitCalculator = $positionGrossPricePerUnitCalculator;
        $this->positionNetPriceCalculator = $positionNetPriceCalculator;
        $this->positionNetPricePerUnitCalculator = $positionNetPricePerUnitCalculator;
    }

    /**
     * @param \Shopware\Models\Order\Detail $invoiceItem
     * @return \Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketPositionDto
     */
    public function create($invoiceItem)
    {
        $position = new CreateInvoiceBasketPositionDto();
        $position->productId = $this->positionProductIdCalculator->calculate($invoiceItem);
        $position->productName = $this->positionProductNameCalculator->calculate($invoiceItem);
        $position->quantity = $this->positionQuantityCalculator->calculate($invoiceItem);
        $position->taxPercent = $this->positionTaxPercentCalculator->calculate($invoiceItem);
        $position->grossPositionTotal = $this->positionGrossPriceCalculator->calculate($invoiceItem);
        $position->netPositionTotal = $this->positionNetPriceCalculator->calculate($invoiceItem);
        $position->grossPricePerUnit = $this->positionGrossPricePerUnitCalculator->calculate($invoiceItem);
        $position->netPricePerUnit = $this->positionNetPricePerUnitCalculator->calculate($invoiceItem);
        return $position;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketPositionDto
     */
    public function createShippingPosition($order)
    {
        $position = new CreateInvoiceBasketPositionDto();
        $position->productId = '0';
        $position->productName = 'Shipping';
        $position->quantity = 1;
        $position->taxPercent = $this->getShippingTaxPercent($order);
        $position->grossPositionTotal = $order->getInvoiceShipping();
        $position->netPositionTotal = $order->getInvoiceShippingNet();
        $position->grossPricePerUnit = $order->getInvoiceShipping();
        $position->netPricePerUnit = $order->getInvoiceShippingNet();
        return $position;
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
