<?php

namespace AxytosKaufAufRechnungShopware5\DataMapping;

use Axytos\ECommerce\DataTransferObjects\InvoiceAddressDto;
use Shopware\Models\Country\Country;
use Shopware\Models\Country\State;
use Shopware\Models\Order\Billing;
use Shopware\Models\Order\Order;

class InvoiceAddressDtoFactory
{
    /**
     * @param \Shopware\Models\Order\Order $order
     * @return \Axytos\ECommerce\DataTransferObjects\InvoiceAddressDto
     */
    public function create($order)
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

    /**
     * @return string|null
     */
    private function getRegion(Billing $billing)
    {
        /** @var ?State */
        $state = $billing->getState();

        if (is_null($state)) {
            return null;
        }

        return $state->getName();
    }

    /**
     * @return string|null
     */
    private function getCountry(Billing $billing)
    {
        /** @var Country */
        $country = $billing->getCountry();

        if (is_null($country)) {
            return null;
        }

        return $country->getIso();
    }
}
