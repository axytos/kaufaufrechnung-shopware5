<?php

declare(strict_types=1);

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
    private PositionProductIdCalculator $positionProductIdCalculator;
    private PositionProductNameCalculator $positionProductNameCalculator;
    private PositionQuantityCalculator $positionQuantityCalculator;
    private PositionTaxPercentCalculator $positionTaxPercentCalculator;
    private PositionGrossPriceCalculator $positionGrossPriceCalculator;
    private PositionGrossPricePerUnitCalculator $positionGrossPricePerUnitCalculator;
    private PositionNetPriceCalculator $positionNetPriceCalculator;
    private PositionNetPricePerUnitCalculator $positionNetPricePerUnitCalculator;

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

    public function create(Detail $invoiceItem): CreateInvoiceBasketPositionDto
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

    public function createShippingPosition(Order $order): CreateInvoiceBasketPositionDto
    {
        $position = new CreateInvoiceBasketPositionDto();
        $position->productId = '0';
        $position->productName = 'Shipping';
        $position->quantity = 1;
        $position->taxPercent = $order->getInvoiceShippingTaxRate();
        $position->grossPositionTotal = $order->getInvoiceShipping();
        $position->netPositionTotal = $order->getInvoiceShippingNet();
        $position->grossPricePerUnit = $order->getInvoiceShipping();
        $position->netPricePerUnit = $order->getInvoiceShippingNet();
        return $position;
    }
}
