<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\BasketDto;
use Shopware\Models\Order\Order;

class BasketDtoFactory
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataMapping\BasketPositionDtoCollectionFactory
     */
    private $basketPositionDtoCollectionFactory;

    public function __construct(BasketPositionDtoCollectionFactory $basketPositionDtoCollectionFactory)
    {
        $this->basketPositionDtoCollectionFactory = $basketPositionDtoCollectionFactory;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\DataTransferObjects\BasketDto
     */
    public function create($order)
    {
        $basketDto = new BasketDto();
        $basketDto->netTotal = $order->getInvoiceAmountNet();
        $basketDto->grossTotal = $order->getInvoiceAmount();
        $basketDto->currency = $order->getCurrency();
        $basketDto->positions = $this->basketPositionDtoCollectionFactory->create($order);
        return $basketDto;
    }
}
