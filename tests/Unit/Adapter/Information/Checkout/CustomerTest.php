<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Information\Tracking;

use AxytosKaufAufRechnungShopware5\Adapter\Information\Checkout\Customer;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Customer\Customer as ShopwareCustomer;

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
     * @before
     */
    public function beforeEach()
    {
        $this->customer = $this->createMock(ShopwareCustomer::class);

        $this->sut = new Customer($this->customer);
    }

    /**
     * @return void
     */
    public function test_getCustomerNumber_returnsCorrectValue()
    {
        $this->customer
            ->method('getNumber')
            ->willReturn('12345');

        $result = $this->sut->getCustomerNumber();

        $this->assertEquals('12345', $result);
    }

    /**
     * @return void
     */
    public function test_getCustomerNumber_returnsNullAsDefault()
    {
        $sut = new Customer(null);

        $this->assertNull($sut->getCustomerNumber());
    }

    /**
     * @return void
     */
    public function test_getDateOfBirth_returnsCorrectValue()
    {
        $date = new DateTime();
        $this->customer
            ->method('getBirthday')
            ->willReturn($date);

        $result = $this->sut->getDateOfBirth();

        $this->assertEquals($date, $result);
    }

    /**
     * @return void
     */
    public function test_getDateOfBirth_returnsNullAsDefault()
    {
        $sut = new Customer(null);

        $this->assertNull($sut->getDateOfBirth());
    }

    /**
     * @return void
     */
    public function test_getEmailAddress_returnsCorrectValue()
    {
        $this->customer
            ->method('getEmail')
            ->willReturn('test@mail.de');

        $result = $this->sut->getEmailAddress();

        $this->assertEquals('test@mail.de', $result);
    }

    /**
     * @return void
     */
    public function test_getEmailAddress_returnsNullAsDefault()
    {
        $sut = new Customer(null);

        $this->assertNull($sut->getEmailAddress());
    }

    /**
     * @return void
     */
    public function test_getCompanyName_returnsCorrectValue()
    {
        $this->customer
            ->method('getCustomerType')
            ->willReturn(ShopwareCustomer::CUSTOMER_TYPE_BUSINESS);
        $this->customer
            ->method('getFirstname')
            ->willReturn('Very Awesome');
        $this->customer
            ->method('getLastname')
            ->willReturn('Company');

        $result = $this->sut->getCompanyName();

        $this->assertEquals('Very Awesome Company', $result);
    }

    /**
     * @return void
     */
    public function test_getCompanyName_returnsNullIfCustomerIsNotACompany()
    {
        $this->customer
            ->method('getCustomerType')
            ->willReturn(ShopwareCustomer::CUSTOMER_TYPE_PRIVATE);

        $this->assertNull($this->sut->getCompanyName());
    }

    /**
     * @return void
     */
    public function test_getCompanyName_returnsNullAsDefault()
    {
        $sut = new Customer(null);

        $this->assertNull($sut->getCompanyName());
    }
}
