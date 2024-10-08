<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information\Tracking;

use AxytosKaufAufRechnungShopware5\Adapter\Information\Checkout\Customer;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Customer\Customer as ShopwareCustomer;

/**
 * @internal
 */
class CustomerTest extends TestCase
{
    /**
     * @var ShopwareCustomer&MockObject
     */
    private $customer;

    /**
     * @var Customer
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
        $this->customer = $this->createMock(ShopwareCustomer::class);

        $this->sut = new Customer($this->customer);
    }

    /**
     * @return void
     */
    public function test_get_customer_number_returns_correct_value()
    {
        $this->customer
            ->method('getNumber')
            ->willReturn('12345')
        ;

        $result = $this->sut->getCustomerNumber();

        $this->assertEquals('12345', $result);
    }

    /**
     * @return void
     */
    public function test_get_customer_number_returns_null_as_default()
    {
        $sut = new Customer(null);

        $this->assertNull($sut->getCustomerNumber());
    }

    /**
     * @return void
     */
    public function test_get_date_of_birth_returns_correct_value()
    {
        $date = new \DateTime();
        $this->customer
            ->method('getBirthday')
            ->willReturn($date)
        ;

        $result = $this->sut->getDateOfBirth();

        $this->assertEquals($date, $result);
    }

    /**
     * @return void
     */
    public function test_get_date_of_birth_returns_null_as_default()
    {
        $sut = new Customer(null);

        $this->assertNull($sut->getDateOfBirth());
    }

    /**
     * @return void
     */
    public function test_get_email_address_returns_correct_value()
    {
        $this->customer
            ->method('getEmail')
            ->willReturn('test@mail.de')
        ;

        $result = $this->sut->getEmailAddress();

        $this->assertEquals('test@mail.de', $result);
    }

    /**
     * @return void
     */
    public function test_get_email_address_returns_null_as_default()
    {
        $sut = new Customer(null);

        $this->assertNull($sut->getEmailAddress());
    }

    /**
     * @return void
     */
    public function test_get_company_name_returns_correct_value()
    {
        $this->customer
            ->method('getCustomerType')
            ->willReturn(ShopwareCustomer::CUSTOMER_TYPE_BUSINESS)
        ;
        $this->customer
            ->method('getFirstname')
            ->willReturn('Very Awesome')
        ;
        $this->customer
            ->method('getLastname')
            ->willReturn('Company')
        ;

        $result = $this->sut->getCompanyName();

        $this->assertEquals('Very Awesome Company', $result);
    }

    /**
     * @return void
     */
    public function test_get_company_name_returns_null_if_customer_is_not_a_company()
    {
        $this->customer
            ->method('getCustomerType')
            ->willReturn(ShopwareCustomer::CUSTOMER_TYPE_PRIVATE)
        ;

        $this->assertNull($this->sut->getCompanyName());
    }

    /**
     * @return void
     */
    public function test_get_company_name_returns_null_as_default()
    {
        $sut = new Customer(null);

        $this->assertNull($sut->getCompanyName());
    }
}
