<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Configuration;

use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutOrderStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Status;

class AfterCheckoutOrderStatusTest extends TestCase
{
    /**
     * @dataProvider getStatusCodeTestCases
     * @param string $value
     * @param int $expectedStatusCode
     * @return void
     */
    #[DataProvider('getStatusCodeTestCases')]
    public function test_getStatusCode_returns_correct_value($value, $expectedStatusCode)
    {
        $afterCheckoutOrderStatus = new AfterCheckoutOrderStatus($value);

        $this->assertEquals($expectedStatusCode, $afterCheckoutOrderStatus->getStatusCode());
    }

    /**
     * @return mixed[]
     */
    public static function getStatusCodeTestCases()
    {
        return [
            [AfterCheckoutOrderStatus::ORDER_STATE_CANCELLED, Status::ORDER_STATE_CANCELLED],
            [AfterCheckoutOrderStatus::ORDER_STATE_OPEN, Status::ORDER_STATE_OPEN],
            [AfterCheckoutOrderStatus::ORDER_STATE_IN_PROCESS, Status::ORDER_STATE_IN_PROCESS],
            [AfterCheckoutOrderStatus::ORDER_STATE_COMPLETED, Status::ORDER_STATE_COMPLETED],
            [AfterCheckoutOrderStatus::ORDER_STATE_PARTIALLY_COMPLETED, Status::ORDER_STATE_PARTIALLY_COMPLETED],
            [AfterCheckoutOrderStatus::ORDER_STATE_CANCELLED_REJECTED, Status::ORDER_STATE_CANCELLED_REJECTED],
            [AfterCheckoutOrderStatus::ORDER_STATE_READY_FOR_DELIVERY, Status::ORDER_STATE_READY_FOR_DELIVERY],
            [AfterCheckoutOrderStatus::ORDER_STATE_PARTIALLY_DELIVERED, Status::ORDER_STATE_PARTIALLY_DELIVERED],
            [AfterCheckoutOrderStatus::ORDER_STATE_COMPLETELY_DELIVERED, Status::ORDER_STATE_COMPLETELY_DELIVERED],
            [AfterCheckoutOrderStatus::ORDER_STATE_CLARIFICATION_REQUIRED, Status::ORDER_STATE_CLARIFICATION_REQUIRED],
        ];
    }

    /**
     * @return void
     */
    public function test_getStatusCode_returns_ORDER_STATE_OPEN_as_default()
    {
        $afterCheckoutOrderStatus = new AfterCheckoutOrderStatus('');

        $this->assertEquals(Status::ORDER_STATE_OPEN, $afterCheckoutOrderStatus->getStatusCode());
    }
}
