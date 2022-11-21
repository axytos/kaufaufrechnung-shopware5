<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Tests\Configuration;

use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutOrderStatus;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Status;

class AfterCheckoutOrderStatusTest extends TestCase
{
    /**
     * @dataProvider getStatusCodeTestCases
     */
    public function test_getStatusCode_returns_correct_value(string $value, int $expectedStatusCode): void
    {
        $afterCheckoutOrderStatus = new AfterCheckoutOrderStatus($value);

        $this->assertEquals($expectedStatusCode, $afterCheckoutOrderStatus->getStatusCode());
    }

    public static function getStatusCodeTestCases(): array
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
}
