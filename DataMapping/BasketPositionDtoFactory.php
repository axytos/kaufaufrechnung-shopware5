<?php

declare(strict_types=1);

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

    public function create(Detail $detail): BasketPositionDto
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

    public function createShippingPosition(Order $order): BasketPositionDto
    {
        $basketPositionDto = new BasketPositionDto();
        $basketPositionDto->productId = '0';
        $basketPositionDto->productName = 'Shipping';
        $basketPositionDto->quantity = 1;
        $basketPositionDto->taxPercent = $order->getInvoiceShippingTaxRate();
        $basketPositionDto->grossPositionTotal = $order->getInvoiceShipping();
        $basketPositionDto->netPositionTotal = $order->getInvoiceShippingNet();
        $basketPositionDto->grossPricePerUnit = $order->getInvoiceShipping();
        $basketPositionDto->netPricePerUnit = $order->getInvoiceShippingNet();
        return $basketPositionDto;
    }
}
