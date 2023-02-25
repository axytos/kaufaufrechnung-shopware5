<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\BasketPositionDto;
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

class BasketPositionDtoFactory
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
     * @param \Shopware\Models\Order\Detail $detail
     * @return \Axytos\ECommerce\DataTransferObjects\BasketPositionDto
     */
    public function create($detail)
    {
        $basketPositionDto = new BasketPositionDto();
        $basketPositionDto->productId = $this->positionProductIdCalculator->calculate($detail);
        $basketPositionDto->productName = $this->positionProductNameCalculator->calculate($detail);
        $basketPositionDto->quantity = $this->positionQuantityCalculator->calculate($detail);
        $basketPositionDto->taxPercent = $this->positionTaxPercentCalculator->calculate($detail);
        $basketPositionDto->grossPositionTotal = $this->positionGrossPriceCalculator->calculate($detail);
        $basketPositionDto->netPositionTotal = $this->positionNetPriceCalculator->calculate($detail);
        $basketPositionDto->grossPricePerUnit = $this->positionGrossPricePerUnitCalculator->calculate($detail);
        $basketPositionDto->netPricePerUnit = $this->positionNetPricePerUnitCalculator->calculate($detail);
        return $basketPositionDto;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\DataTransferObjects\BasketPositionDto
     */
    public function createShippingPosition($order)
    {
        $basketPositionDto = new BasketPositionDto();
        $basketPositionDto->productId = '0';
        $basketPositionDto->productName = 'Shipping';
        $basketPositionDto->quantity = 1;
        $basketPositionDto->taxPercent = $this->getShippingTaxPercent($order);
        $basketPositionDto->grossPositionTotal = $order->getInvoiceShipping();
        $basketPositionDto->netPositionTotal = $order->getInvoiceShippingNet();
        $basketPositionDto->grossPricePerUnit = $order->getInvoiceShipping();
        $basketPositionDto->netPricePerUnit = $order->getInvoiceShippingNet();
        return $basketPositionDto;
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
