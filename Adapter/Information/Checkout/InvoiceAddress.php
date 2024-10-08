<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information\Checkout;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\InvoiceAddressInterface;

class InvoiceAddress implements InvoiceAddressInterface
{
    /**
     * @var \Shopware\Models\Order\Billing|null
     */
    private $billing;

    /**
     * @param \Shopware\Models\Order\Billing|null $billing
     */
    public function __construct($billing)
    {
        $this->billing = $billing;
    }

    /**
     * @return string|null
     */
    public function getCompanyName()
    {
        if (is_null($this->billing)) {
            return null;
        }

        return $this->billing->getCompany();
    }

    /**
     * @return string|null
     */
    public function getSalutation()
    {
        if (is_null($this->billing)) {
            return null;
        }

        return $this->billing->getSalutation();
    }

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        if (is_null($this->billing)) {
            return null;
        }

        return $this->billing->getFirstName();
    }

    /**
     * @return string|null
     */
    public function getLastName()
    {
        if (is_null($this->billing)) {
            return null;
        }

        return $this->billing->getLastName();
    }

    /**
     * @return string
     */
    public function getZipCode()
    {
        if (is_null($this->billing)) {
            return '';
        }

        return $this->billing->getZipCode();
    }

    /**
     * @return string
     */
    public function getCityName()
    {
        if (is_null($this->billing)) {
            return '';
        }

        return $this->billing->getCity();
    }

    /**
     * @return string|null
     */
    public function getRegionName()
    {
        if (is_null($this->billing)) {
            return null;
        }

        $state = $this->billing->getState();

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
        if (is_null($this->billing)) {
            return '';
        }

        /**
         * @var ?\Shopware\Models\Country\Country
         *                                        can be null in some versions of shopware
         */
        $country = $this->billing->getCountry();

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
        if (is_null($this->billing)) {
            return null;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getStreet()
    {
        if (is_null($this->billing)) {
            return null;
        }

        return $this->billing->getStreet();
    }

    /**
     * @return string|null
     */
    public function getAdditionalAddressLine2()
    {
        if (is_null($this->billing)) {
            return '';
        }

        return strval($this->billing->getAdditionalAddressLine1());
    }

    /**
     * @return string|null
     */
    public function getAdditionalAddressLine3()
    {
        if (is_null($this->billing)) {
            return '';
        }

        return strval($this->billing->getAdditionalAddressLine2());
    }

    /**
     * @return string|null
     */
    public function getAdditionalAddressLine4()
    {
        if (is_null($this->billing)) {
            return '';
        }

        return '';
    }
}
