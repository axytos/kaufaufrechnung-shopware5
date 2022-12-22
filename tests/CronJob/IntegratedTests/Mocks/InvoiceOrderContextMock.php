<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob\IntegratedTests\Mocks;

use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use DateTime;

class InvoiceOrderContextMock implements InvoiceOrderContextInterface
{
    /**
     * @var ShopSystemOrderMock
     */
    private $shopSystemOrderMock;

    /**
     * @param ShopSystemOrderMock $shopSystemOrderMock
     */
    public function __construct($shopSystemOrderMock)
    {
        $this->shopSystemOrderMock = $shopSystemOrderMock;
    }

    /**
     * @return ShopSystemOrderMock
     */
    public function getShopSystemOrderMock()
    {
        return $this->shopSystemOrderMock;
    }

    /**
     * @return string
     */
    public function getOrderNumber()
    {
        return '';
    }
    /**
     * @return string
     */
    public function getOrderInvoiceNumber()
    {
        return '';
    }
    /**
     * @return \DateTimeInterface
     */
    public function getOrderDateTime()
    {
        return new DateTime();
    }
    /**
     * @return \Axytos\ECommerce\DataTransferObjects\CustomerDataDto
     */
    public function getPersonalData()
    {
        return new \Axytos\ECommerce\DataTransferObjects\CustomerDataDto();
    }
    /**
     * @return \Axytos\ECommerce\DataTransferObjects\InvoiceAddressDto
     */
    public function getInvoiceAddress()
    {
        return new \Axytos\ECommerce\DataTransferObjects\InvoiceAddressDto();
    }
    /**
     * @return \Axytos\ECommerce\DataTransferObjects\DeliveryAddressDto
     */
    public function getDeliveryAddress()
    {
        return new \Axytos\ECommerce\DataTransferObjects\DeliveryAddressDto();
    }
    /**
     * @return \Axytos\ECommerce\DataTransferObjects\BasketDto
     */
    public function getBasket()
    {
        return new \Axytos\ECommerce\DataTransferObjects\BasketDto();
    }
    /**
     * @return \Axytos\ECommerce\DataTransferObjects\RefundBasketDto
     */
    public function getRefundBasket()
    {
        return new \Axytos\ECommerce\DataTransferObjects\RefundBasketDto();
    }
    /**
     * @return \Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketDto
     */
    public function getCreateInvoiceBasket()
    {
        return new \Axytos\ECommerce\DataTransferObjects\CreateInvoiceBasketDto();
    }
    /**
     * @return \Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDtoCollection
     */
    public function getShippingBasketPositions()
    {
        return new \Axytos\ECommerce\DataTransferObjects\ShippingBasketPositionDtoCollection();
    }
    /**
     * @return mixed[]
     */
    public function getPreCheckResponseData()
    {
        return [];
    }
    /**
     * @param mixed[] $data
     * @return void
     */
    public function setPreCheckResponseData($data)
    {
    }
    /**
     * @return \Axytos\ECommerce\DataTransferObjects\ReturnPositionModelDtoCollection
     */
    public function getReturnPositions()
    {
        return new \Axytos\ECommerce\DataTransferObjects\ReturnPositionModelDtoCollection();
    }
    /**
     * @return float
     */
    public function getDeliveryWeight()
    {
        return 0;
    }
    /**
     * @return string[]
     */
    public function getTrackingIds()
    {
        return [];
    }
    /**
     * @return string
     */
    public function getLogistician()
    {
        return '';
    }
}
