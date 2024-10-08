<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information\Tracking;

use AxytosKaufAufRechnungShopware5\Adapter\Information\Tracking\DeliveryAddress;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Country\Country;
use Shopware\Models\Country\State;
use Shopware\Models\Order\Shipping;

/**
 * @internal
 */
class DeliveryAddressTest extends TestCase
{
    /**
     * @var Shipping&MockObject
     */
    private $shipping;

    /**
     * @var DeliveryAddress
     */
    private $sut;

    /**
     * @return void
     *
     * @before
     */
    #[Before]
    public function beforeEach()
    {
        $this->shipping = $this->createMock(Shipping::class);

        $this->sut = new DeliveryAddress($this->shipping);
    }

    /**
     * @return void
     */
    public function test_get_company_name_returns_correct_value()
    {
        $this->shipping
            ->method('getCompany')
            ->willReturn('Musterfabrik')
        ;

        $result = $this->sut->getCompanyName();

        $this->assertEquals('Musterfabrik', $result);
    }

    /**
     * @return void
     */
    public function test_get_company_name_returns_null_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame(null, $sut->getCompanyName());
    }

    /**
     * @return void
     */
    public function test_get_salutation_returns_correct_value()
    {
        $this->shipping
            ->method('getSalutation')
            ->willReturn('Herr')
        ;

        $result = $this->sut->getSalutation();

        $this->assertEquals('Herr', $result);
    }

    /**
     * @return void
     */
    public function test_get_salutation_returns_null_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame(null, $sut->getSalutation());
    }

    /**
     * @return void
     */
    public function test_get_first_name_returns_correct_value()
    {
        $this->shipping
            ->method('getFirstName')
            ->willReturn('Max')
        ;

        $result = $this->sut->getFirstName();

        $this->assertEquals('Max', $result);
    }

    /**
     * @return void
     */
    public function test_get_first_name_returns_null_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame(null, $sut->getFirstName());
    }

    /**
     * @return void
     */
    public function test_get_last_name_returns_correct_value()
    {
        $this->shipping
            ->method('getLastName')
            ->willReturn('Muster')
        ;

        $result = $this->sut->getLastName();

        $this->assertEquals('Muster', $result);
    }

    /**
     * @return void
     */
    public function test_get_last_name_returns_null_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame(null, $sut->getLastName());
    }

    /**
     * @return void
     */
    public function test_get_zip_code_returns_correct_value()
    {
        $this->shipping
            ->method('getZipCode')
            ->willReturn('12345')
        ;

        $result = $this->sut->getZipCode();

        $this->assertEquals('12345', $result);
    }

    /**
     * @return void
     */
    public function test_get_zip_code_returns_empty_string_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame('', $sut->getZipCode());
    }

    /**
     * @return void
     */
    public function test_get_city_name_returns_correct_value()
    {
        $this->shipping
            ->method('getCity')
            ->willReturn('Musterhausen')
        ;

        $result = $this->sut->getCityName();

        $this->assertEquals('Musterhausen', $result);
    }

    /**
     * @return void
     */
    public function test_get_city_name_returns_empty_string_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame('', $sut->getCityName());
    }

    /**
     * @return void
     */
    public function test_get_region_name_returns_correct_value()
    {
        /** @var State&MockObject */
        $state = $this->createMock(State::class);
        $this->shipping
            ->method('getState')
            ->willReturn($state)
        ;
        $state
            ->method('getName')
            ->willReturn('Musterland')
        ;

        $result = $this->sut->getRegionName();

        $this->assertEquals('Musterland', $result);
    }

    /**
     * @return void
     */
    public function test_get_region_name_returns_null_if_state_is_not_set()
    {
        $this->shipping
            ->method('getState')
            ->willReturn(null)
        ;

        $result = $this->sut->getRegionName();

        $this->assertSame(null, $result);
    }

    /**
     * @return void
     */
    public function test_get_region_name_returns_null_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame(null, $sut->getRegionName());
    }

    /**
     * @return void
     */
    public function test_get_country_code_returns_correct_value()
    {
        /** @var Country&MockObject */
        $country = $this->createMock(Country::class);
        $this->shipping
            ->method('getCountry')
            ->willReturn($country)
        ;
        $country
            ->method('getIso')
            ->willReturn('DE')
        ;

        $result = $this->sut->getCountryCode();

        $this->assertEquals('DE', $result);
    }

    /**
     * @return void
     */
    public function test_get_country_code_returns_empty_string_if_country_is_not_set()
    {
        $this->shipping
            ->method('getCountry')
            ->willReturn(null)
        ;

        $result = $this->sut->getCountryCode();

        $this->assertEquals('', $result);
    }

    /**
     * @return void
     */
    public function test_get_country_code_returns_empty_string_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame('', $sut->getCountryCode());
    }

    /**
     * @return void
     */
    public function test_get_vat_id_returns_correct_value()
    {
        $result = $this->sut->getVATId();

        $this->assertNull($result);
    }

    /**
     * @return void
     */
    public function test_get_vat_id_returns_null_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame(null, $sut->getVATId());
    }

    /**
     * @return void
     */
    public function test_get_street_returns_correct_value()
    {
        $this->shipping
            ->method('getStreet')
            ->willReturn('Musterstraße 1a')
        ;

        $result = $this->sut->getStreet();

        $this->assertEquals('Musterstraße 1a', $result);
    }

    /**
     * @return void
     */
    public function test_get_street_returns_null_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame(null, $sut->getStreet());
    }

    /**
     * @return void
     */
    public function test_get_additional_address_line2_returns_correct_value()
    {
        $this->shipping
            ->method('getAdditionalAddressLine1')
            ->willReturn('Erster Stock')
        ;

        $result = $this->sut->getAdditionalAddressLine2();

        $this->assertEquals('Erster Stock', $result);
    }

    /**
     * @return void
     */
    public function test_get_additional_address_line2_returns_null_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame('', $sut->getAdditionalAddressLine2());
    }

    /**
     * @return void
     */
    public function test_get_additional_address_line3_returns_correct_value()
    {
        $this->shipping
            ->method('getAdditionalAddressLine2')
            ->willReturn('Zimmer 404')
        ;

        $result = $this->sut->getAdditionalAddressLine3();

        $this->assertEquals('Zimmer 404', $result);
    }

    /**
     * @return void
     */
    public function test_get_additional_address_line3_returns_null_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame('', $sut->getAdditionalAddressLine3());
    }

    /**
     * @return void
     */
    public function test_get_additional_address_line4_returns_correct_value()
    {
        $result = $this->sut->getAdditionalAddressLine4();

        $this->assertSame('', $result);
    }

    /**
     * @return void
     */
    public function test_get_additional_address_line4_returns_null_as_default()
    {
        $sut = new DeliveryAddress(null);

        $this->assertSame('', $sut->getAdditionalAddressLine4());
    }
}
