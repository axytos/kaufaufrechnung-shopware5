<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\CompanyDto;
use Axytos\ECommerce\DataTransferObjects\CustomerDataDto;
use Shopware\Models\Customer\Customer;
use Shopware\Models\Order\Order;

class CustomerDataDtoFactory
{
    public function create(Order $order): CustomerDataDto
    {
        /** @var Customer */
        $customer = $order->getCustomer();

        $customerDataDto = new CustomerDataDto();
        $customerDataDto->externalCustomerId = $customer->getNumber();
        $customerDataDto->email = $customer->getEmail();
        $customerDataDto->dateOfBirth = $customer->getBirthday();

        if ($customer->getCustomerType() === Customer::CUSTOMER_TYPE_BUSINESS) {
            $customerDataDto->company = new CompanyDto();
            $customerDataDto->company->name = $customer->getFirstname() . ' ' . $customer->getLastname();
        }

        return $customerDataDto;
    }
}
