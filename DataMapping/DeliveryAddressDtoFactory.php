<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\DeliveryAddressDto;
use Shopware\Models\Country\Country;
use Shopware\Models\Country\State;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Shipping;

class DeliveryAddressDtoFactory
{
    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\DataTransferObjects\DeliveryAddressDto
     */
    public function create($order)
    {
        /** @var Shipping */
        $shipping = $order->getShipping();

        $deliveryAddressDto = new DeliveryAddressDto();
        $deliveryAddressDto->company = strval($shipping->getCompany());
        $deliveryAddressDto->salutation = strval($shipping->getSalutation());
        $deliveryAddressDto->firstname = strval($shipping->getFirstName());
        $deliveryAddressDto->lastname = strval($shipping->getLastName());
        $deliveryAddressDto->zipCode = strval($shipping->getZipCode());
        $deliveryAddressDto->city = strval($shipping->getCity());
        $deliveryAddressDto->region = strval($this->getRegion($shipping));
        $deliveryAddressDto->country = strval($this->getCountry($shipping));
        $deliveryAddressDto->addressLine1 = strval($shipping->getStreet());
        $deliveryAddressDto->addressLine2 = strval($shipping->getAdditionalAddressLine2());
        return $deliveryAddressDto;
    }

    /**
     * @return string|null
     */
    private function getRegion(Shipping $shipping)
    {
        /** @var ?State */
        $state = $shipping->getState();

        if (is_null($state)) {
            return null;
        }

        return $state->getName();
    }

    /**
     * @return string|null
     */
    private function getCountry(Shipping $shipping)
    {
        /** @var Country */
        $country = $shipping->getCountry();
        return $country->getIso();
    }
}
