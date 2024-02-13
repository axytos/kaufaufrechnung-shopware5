<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\CheckoutInformationInterface;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Checkout\Customer;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Checkout\DeliveryAddress;
use AxytosKaufAufRechnungShopware5\Adapter\Information\Checkout\InvoiceAddress;
use AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory;
use AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order;

class CheckoutInformation implements CheckoutInformationInterface
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\UnifiedShopwareModel\Order
     */
    private $order;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Adapter\Common\BasketFactory
     */
    private $basketFactory;

    public function __construct(
        Order $order,
        BasketFactory $basketFactory
    ) {
        $this->order = $order;
        $this->basketFactory = $basketFactory;
    }

    /**
     * @param mixed[] $preCheckResponseData
     * @return void
     */
    public function savePreCheckResponseData($preCheckResponseData)
    {
        $preCheckResponseJson = strval(json_encode($preCheckResponseData));

        $orderAttributes = $this->order->getAttributes();
        $orderAttributes->setAxytosKaufAufRechnungPrecheckResponse($preCheckResponseJson);
        $orderAttributes->persist();
    }

    /**
     * @return mixed[] $preCheckResponseData
     */
    public function getPreCheckResponseData()
    {
        $orderAttributes = $this->order->getAttributes();
        $preCheckResponseJson = $orderAttributes->getAxytosKaufAufRechnungPrecheckResponse();
        $preCheckResponse = json_decode($preCheckResponseJson, true);
        /** @phpstan-ignore-next-line */
        return $preCheckResponse;
    }

    /**
     * @return string|int
     */
    public function getOrderNumber()
    {
        return $this->order->getNumber();
    }

    /**
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\CustomerInterface
     */
    public function getCustomer()
    {
        return new Customer($this->order->getCustomer());
    }

    /**
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\InvoiceAddressInterface
     */
    public function getInvoiceAddress()
    {
        return new InvoiceAddress($this->order->getBilling());
    }

    /**
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\DeliveryAddressInterface
     */
    public function getDeliveryAddress()
    {
        return new DeliveryAddress($this->order->getShipping());
    }

    /**
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\BasketInterface
     */
    public function getBasket()
    {
        return $this->basketFactory->create($this->order);
    }
}
