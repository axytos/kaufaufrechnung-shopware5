<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Configuration;

use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutPaymentStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Status;

/**
 * @internal
 */
class AfterCheckoutPaymentStatusTest extends TestCase
{
    /**
     * @dataProvider getStatusCodeTestCases
     *
     * @param string $value
     * @param int    $expectedStatusCode
     *
     * @return void
     */
    #[DataProvider('getStatusCodeTestCases')]
    public function test_get_status_code_returns_correct_value($value, $expectedStatusCode)
    {
        $afterCheckoutOrderStatus = new AfterCheckoutPaymentStatus($value);

        $this->assertEquals($expectedStatusCode, $afterCheckoutOrderStatus->getStatusCode());
    }

    /**
     * @return mixed[]
     */
    public static function getStatusCodeTestCases()
    {
        return [
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_PARTIALLY_INVOICED, Status::PAYMENT_STATE_PARTIALLY_INVOICED],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_COMPLETELY_INVOICED, Status::PAYMENT_STATE_COMPLETELY_INVOICED],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_PARTIALLY_PAID, Status::PAYMENT_STATE_PARTIALLY_PAID],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_COMPLETELY_PAID, Status::PAYMENT_STATE_COMPLETELY_PAID],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_1ST_REMINDER, Status::PAYMENT_STATE_1ST_REMINDER],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_2ND_REMINDER, Status::PAYMENT_STATE_2ND_REMINDER],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_3RD_REMINDER, Status::PAYMENT_STATE_3RD_REMINDER],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_ENCASHMENT, Status::PAYMENT_STATE_ENCASHMENT],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_OPEN, Status::PAYMENT_STATE_OPEN],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_RESERVED, Status::PAYMENT_STATE_RESERVED],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_DELAYED, Status::PAYMENT_STATE_DELAYED],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_RE_CREDITING, Status::PAYMENT_STATE_RE_CREDITING],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_REVIEW_NECESSARY, Status::PAYMENT_STATE_REVIEW_NECESSARY],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_NO_CREDIT_APPROVED, Status::PAYMENT_STATE_NO_CREDIT_APPROVED],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_THE_CREDIT_HAS_BEEN_PRELIMINARILY_ACCEPTED, Status::PAYMENT_STATE_THE_CREDIT_HAS_BEEN_PRELIMINARILY_ACCEPTED],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_THE_CREDIT_HAS_BEEN_ACCEPTED, Status::PAYMENT_STATE_THE_CREDIT_HAS_BEEN_ACCEPTED],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_THE_PAYMENT_HAS_BEEN_ORDERED, Status::PAYMENT_STATE_THE_PAYMENT_HAS_BEEN_ORDERED],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_A_TIME_EXTENSION_HAS_BEEN_REGISTERED, Status::PAYMENT_STATE_A_TIME_EXTENSION_HAS_BEEN_REGISTERED],
            [AfterCheckoutPaymentStatus::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED, Status::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED],
        ];
    }

    /**
     * @return void
     */
    public function test_get_status_code_returns_orde_r_stat_e_ope_n_as_default()
    {
        $afterCheckoutOrderStatus = new AfterCheckoutPaymentStatus('');

        $this->assertEquals(Status::ORDER_STATE_OPEN, $afterCheckoutOrderStatus->getStatusCode());
    }
}
