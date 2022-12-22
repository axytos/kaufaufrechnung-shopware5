<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob\Commands;

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use AxytosKaufAufRechnungShopware5\CronJob\Commands\ReportCancel;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReportCancelTest extends TestCase
{
    /**
     * @var \Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface&MockObject
     */
    private $invoiceClient;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\Commands\ReportCancel
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->invoiceClient = $this->createMock(InvoiceClientInterface::class);

        $this->sut = new ReportCancel(
            $this->invoiceClient,
            $this->createMock(LoggerAdapterInterface::class)
        );
    }

    /**
     * @dataProvider execute_cases
     * @param bool $hasCancelReported
     * @param bool $hasBeenCanceled
     * @param \PHPUnit\Framework\MockObject\Rule\InvokedCount $reportRefundInvocations
     * @return void
     */
    public function test_execute_reports_cancel($hasCancelReported, $hasBeenCanceled, $reportRefundInvocations)
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $shopSystemOrder->method('hasCancelReported')->willReturn($hasCancelReported);
        $shopSystemOrder->method('hasBeenCanceled')->willReturn($hasBeenCanceled);

        $reportData = $this->createMock(InvoiceOrderContextInterface::class);
        $shopSystemOrder->method('getCancelReportData')->willReturn($reportData);

        $this->invoiceClient->expects($reportRefundInvocations)->method('cancelOrder')->with($reportData);

        $this->sut->execute($shopSystemOrder);
    }

    /**
     * @dataProvider execute_cases
     * @param bool $hasCancelReported
     * @param bool $hasBeenCanceled
     * @param \PHPUnit\Framework\MockObject\Rule\InvokedCount $reportRefundInvocations
     * @return void
     */
    public function test_execute_saves_cancel_reported($hasCancelReported, $hasBeenCanceled, $reportRefundInvocations)
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $shopSystemOrder->method('hasCancelReported')->willReturn($hasCancelReported);
        $shopSystemOrder->method('hasBeenCanceled')->willReturn($hasBeenCanceled);

        $shopSystemOrder->expects($reportRefundInvocations)->method('saveHasCancelReported');

        $this->sut->execute($shopSystemOrder);
    }

    /**
     * @return mixed[]
     */
    public function execute_cases()
    {
        return [
            'already reported and canceled     -> will not report' => [true, true, $this->never()],
            'already reported and not canceled -> will not report' => [true, false, $this->never()],
            'not yet reported and canceled     -> will report' => [false, true, $this->once()],
            'not yet reported and not canceled -> will not report' => [false, false, $this->never()],
        ];
    }
}
