<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\InvoiceAddressDto;
use Shopware\Models\Country\Country;
use Shopware\Models\Country\State;
use Shopware\Models\Order\Billing;
use Shopware\Models\Order\Order;

class InvoiceAddressDtoFactory
{
    public function create(Order $order): InvoiceAddressDto
    {
        /** @var Billing */
        $billing = $order->getBilling();

        $invoiceAddressDto = new InvoiceAddressDto();
        $invoiceAddressDto->company = strval($billing->getCompany());
        $invoiceAddressDto->salutation = strval($billing->getSalutation());
        $invoiceAddressDto->firstname = strval($billing->getFirstName());
        $invoiceAddressDto->lastname = strval($billing->getLastName());
        $invoiceAddressDto->zipCode = strval($billing->getZipCode());
        $invoiceAddressDto->city = strval($billing->getCity());
        $invoiceAddressDto->region = strval($this->getRegion($billing));
        $invoiceAddressDto->country = strval($this->getCountry($billing));
        $invoiceAddressDto->vatId = strval($billing->getVatId());
        $invoiceAddressDto->addressLine1 = strval($billing->getStreet());
        $invoiceAddressDto->addressLine2 = strval($billing->getAdditionalAddressLine2());
        return $invoiceAddressDto;
    }

    private function getRegion(Billing $billing): ?string
    {
        /** @var ?State */
        $state = $billing->getState();

        if (is_null($state)) {
            return null;
        }

        return $state->getName();
    }

    private function getCountry(Billing $billing): ?string
    {
        /** @var Country */
        $country = $billing->getCountry();
        return $country->getIso();
    }
}
