<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob\IntegratedTests;

include_once __DIR__ . '/OrderSyncWorkerIntegratedTestCase.php';

class ReportRefundTest extends OrderSyncWorkerIntegratedTestCase
{
    /**
     * @return void
     */
    public function test()
    {
        $this->executeTestCases([[
            'order' => [

                'hasCreateInvoiceReported' => true,
                'hasBeenInvoiced' => false,

                'hasCancelReported' => false,
                'hasBeenCanceled' => false,

                'hasRefundReported' => false,
                'hasBeenRefunded' => true,

                'hasShippingReported' => false,
                'hasBeenShipped' => false,

                'hasNewTrackingInformation' => false,

            ],
            'expected' => [
                'reportCancel' => false,
                'reportCreateInvoice' => false,
                'reportRefund' => true,
                'reportShipping' => false,
                'reportTrackingInformation' => false,
            ]
            ],[
                'order' => [

                    'hasCreateInvoiceReported' => true,
                    'hasBeenInvoiced' => false,

                    'hasCancelReported' => false,
                    'hasBeenCanceled' => false,

                    'hasRefundReported' => false,
                    'hasBeenRefunded' => true,

                    'hasShippingReported' => false,
                    'hasBeenShipped' => false,

                    'hasNewTrackingInformation' => false,

                ],
                'expected' => [
                    'reportCancel' => false,
                    'reportCreateInvoice' => false,
                    'reportRefund' => true,
                    'reportShipping' => false,
                    'reportTrackingInformation' => false,
                ]
            ]]);
    }
}
