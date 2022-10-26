<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\BasketDto;
use Shopware\Models\Order\Order;

class BasketDtoFactory
{
    private BasketPositionDtoCollectionFactory $basketPositionDtoCollectionFactory;

    public function __construct(BasketPositionDtoCollectionFactory $basketPositionDtoCollectionFactory)
    {
        $this->basketPositionDtoCollectionFactory = $basketPositionDtoCollectionFactory;
    }

    public function create(Order $order): BasketDto
    {
        $basketDto = new BasketDto();
        $basketDto->netTotal = $order->getInvoiceAmountNet();
        $basketDto->grossTotal = $order->getInvoiceAmount();
        $basketDto->currency = $order->getCurrency();
        $basketDto->positions = $this->basketPositionDtoCollectionFactory->create($order);
        return $basketDto;
    }
}
