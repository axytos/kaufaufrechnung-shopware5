<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Information\Checkout;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Information\Checkout\CustomerInterface;
use Shopware\Models\Customer\Customer as ShopwareCustomer;

class Customer implements CustomerInterface
{
    /**
     * @var ShopwareCustomer|null
     */
    private $customer;

    /**
     * @param ShopwareCustomer|null $customer
     */
    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return string|null
     */
    public function getCustomerNumber()
    {
        if (is_null($this->customer)) {
            return null;
        }

        return $this->customer->getNumber();
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateOfBirth()
    {
        if (is_null($this->customer)) {
            return null;
        }

        return $this->customer->getBirthday();
    }

    /**
     * @return string|null
     */
    public function getEmailAddress()
    {
        if (is_null($this->customer)) {
            return null;
        }

        return $this->customer->getEmail();
    }

    /**
     * @return string|null
     */
    public function getCompanyName()
    {
        if (is_null($this->customer)) {
            return null;
        }

        $customerType = $this->customer->getCustomerType();

        if (ShopwareCustomer::CUSTOMER_TYPE_BUSINESS === $customerType) {
            return $this->customer->getFirstname() . ' ' . $this->customer->getLastname();
        }

        return null;
    }
}
