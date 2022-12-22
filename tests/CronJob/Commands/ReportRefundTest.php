<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob\Commands;

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use AxytosKaufAufRechnungShopware5\CronJob\Commands\ReportRefund;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReportRefundTest extends TestCase
{
    /**
     * @var \Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface&MockObject
     */
    private $invoiceClient;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\Commands\ReportRefund
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->invoiceClient = $this->createMock(InvoiceClientInterface::class);

        $this->sut = new ReportRefund(
            $this->invoiceClient,
            $this->createMock(LoggerAdapterInterface::class)
        );
    }

    /**
     * @dataProvider execute_cases
     * @param bool $hasCreateInvoiceReported
     * @param bool $hasRefundReported
     * @param bool $hasBeenRefunded
     * @param \PHPUnit\Framework\MockObject\Rule\InvokedCount $reportRefundInvocations
     * @return void
     */
    public function test_execute_reports_refund($hasCreateInvoiceReported, $hasRefundReported, $hasBeenRefunded, $reportRefundInvocations)
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $shopSystemOrder->method('hasCreateInvoiceReported')->willReturn($hasCreateInvoiceReported);
        $shopSystemOrder->method('hasRefundReported')->willReturn($hasRefundReported);
        $shopSystemOrder->method('hasBeenRefunded')->willReturn($hasBeenRefunded);

        $reportData = $this->createMock(InvoiceOrderContextInterface::class);
        $shopSystemOrder->method('getRefundReportData')->willReturn($reportData);

        $this->invoiceClient->expects($reportRefundInvocations)->method('refund')->with($reportData);

        $this->sut->execute($shopSystemOrder);
    }

    /**
     * @dataProvider execute_cases
     * @param bool $hasCreateInvoiceReported
     * @param bool $hasRefundReported
     * @param bool $hasBeenRefunded
     * @param \PHPUnit\Framework\MockObject\Rule\InvokedCount $reportRefundInvocations
     * @return void
     */
    public function test_execute_saves_refund_reported($hasCreateInvoiceReported, $hasRefundReported, $hasBeenRefunded, $reportRefundInvocations)
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $shopSystemOrder->method('hasCreateInvoiceReported')->willReturn($hasCreateInvoiceReported);
        $shopSystemOrder->method('hasRefundReported')->willReturn($hasRefundReported);
        $shopSystemOrder->method('hasBeenRefunded')->willReturn($hasBeenRefunded);

        $shopSystemOrder->expects($reportRefundInvocations)->method('saveHasRefundReported');

        $this->sut->execute($shopSystemOrder);
    }

    /**
     * @return mixed[]
     */
    public function execute_cases()
    {
        return [
            'invoice reported: already reported and refunded        -> will report' => [true, true, true, $this->never()],
            'invoice reported: already reported and not refunded    -> will report' => [true, true, false, $this->never()],
            'invoice reported: not yet reported and refunded        -> will report' => [true, false, true, $this->once()],
            'invoice reported: not yet reported and not refunded    -> will report' => [true, false, false, $this->never()],
            'no invoice reported: already reported and refunded     -> will report' => [false, true, true, $this->never()],
            'no invoice reported: already reported and not refunded -> will report' => [false, true, false, $this->never()],
            'no invoice reported: not yet reported and refunded     -> will report' => [false, false, true, $this->never()],
            'no invoice reported: not yet reported and not refunded -> will report' => [false, false, false, $this->never()],
        ];
    }
}
