<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information\Tracking;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Tracking\DeliveryAddressInterface;

class DeliveryAddress implements DeliveryAddressInterface
{
    /**
     * @var \Shopware\Models\Order\Shipping|null
     */
    private $shipping;

    /**
     * @param \Shopware\Models\Order\Shipping|null $shipping
     */
    public function __construct($shipping)
    {
        $this->shipping = $shipping;
    }

    /**
     * @return string|null
     */
    public function getCompanyName()
    {
        if (is_null($this->shipping)) {
            return null;
        }

        return $this->shipping->getCompany();
    }

    /**
     * @return string|null
     */
    public function getSalutation()
    {
        if (is_null($this->shipping)) {
            return null;
        }

        return $this->shipping->getSalutation();
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        if (is_null($this->shipping)) {
            return null;
        }

        return $this->shipping->getFirstName();
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        if (is_null($this->shipping)) {
            return null;
        }

        return $this->shipping->getLastName();
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        if (is_null($this->shipping)) {
            return '';
        }

        return $this->shipping->getZipCode();
    }

    /**
     * @return string
     */
    public function getCityName()
    {
        if (is_null($this->shipping)) {
            return '';
        }

        return $this->shipping->getCity();
    }

    /**
     * @return string|null
     */
    public function getRegionName()
    {
        if (is_null($this->shipping)) {
            return null;
        }

        $state = $this->shipping->getState();

        if (is_null($state)) {
            return null;
        }

        return $state->getName();
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        if (is_null($this->shipping)) {
            return '';
        }

        /**
         * @var ?\Shopware\Models\Country\Country
         * can be null in some versions of shopware
         */
        $country = $this->shipping->getCountry();

        if (is_null($country)) {
            return '';
        }

        return strval($country->getIso());
    }

    /**
     * @return string|null
     */
    public function getVATId()
    {
        if (is_null($this->shipping)) {
            return null;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getStreet()
    {
        if (is_null($this->shipping)) {
            return null;
        }

        return $this->shipping->getStreet();
    }

    /**
     * @return string|null
     */
    public function getAdditionalAddressLine2()
    {
        if (is_null($this->shipping)) {
            return '';
        }

        return strval($this->shipping->getAdditionalAddressLine1());
    }

    /**
     * @return string|null
     */
    public function getAdditionalAddressLine3()
    {
        if (is_null($this->shipping)) {
            return '';
        }

        return strval($this->shipping->getAdditionalAddressLine2());
    }

    /**
     * @return string|null
     */
    public function getAdditionalAddressLine4()
    {
        if (is_null($this->shipping)) {
            return '';
        }

        return '';
    }
}
