<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob\Commands;

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use AxytosKaufAufRechnungShopware5\CronJob\Commands\ReportCreateInvoice;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReportCreateInvoiceTest extends TestCase
{
    /**
     * @var \Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface&MockObject
     */
    private $invoiceClient;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\Commands\ReportCreateInvoice
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->invoiceClient = $this->createMock(InvoiceClientInterface::class);

        $this->sut = new ReportCreateInvoice(
            $this->invoiceClient,
            $this->createMock(LoggerAdapterInterface::class)
        );
    }

    /**
     * @dataProvider execute_cases
     * @param bool $hasCreateInvoiceReported
     * @param bool $hasBeenInvoiced
     * @param \PHPUnit\Framework\MockObject\Rule\InvokedCount $reportCreateInvoiceInvocations
     * @return void
     */
    public function test_execute_reports_create_invoice($hasCreateInvoiceReported, $hasBeenInvoiced, $reportCreateInvoiceInvocations)
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $shopSystemOrder->method('hasCreateInvoiceReported')->willReturn($hasCreateInvoiceReported);
        $shopSystemOrder->method('hasBeenInvoiced')->willReturn($hasBeenInvoiced);

        $reportData = $this->createMock(InvoiceOrderContextInterface::class);
        $shopSystemOrder->method('getCreateInvoiceReportData')->willReturn($reportData);

        $this->invoiceClient->expects($reportCreateInvoiceInvocations)->method('createInvoice')->with($reportData);

        $this->sut->execute($shopSystemOrder);
    }

    /**
     * @dataProvider execute_cases
     * @param bool $hasCreateInvoiceReported
     * @param bool $hasBeenInvoiced
     * @param \PHPUnit\Framework\MockObject\Rule\InvokedCount $reportCreateInvoiceInvocations
     * @return void
     */
    public function test_execute_saves_create_invoice_reported($hasCreateInvoiceReported, $hasBeenInvoiced, $reportCreateInvoiceInvocations)
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $shopSystemOrder->method('hasCreateInvoiceReported')->willReturn($hasCreateInvoiceReported);
        $shopSystemOrder->method('hasBeenInvoiced')->willReturn($hasBeenInvoiced);

        $shopSystemOrder->expects($reportCreateInvoiceInvocations)->method('saveHasCreateInvoiceReported');

        $this->sut->execute($shopSystemOrder);
    }

    /**
     * @return mixed[]
     */
    public function execute_cases()
    {
        return [
            'already reported and invoiced     -> will not report' => [true, true, $this->never()],
            'already reported and not invoiced -> will not report' => [true, false, $this->never()],
            'not yet reported and invoiced     -> will report' => [false, true, $this->once()],
            'not yet reported and not invoiced -> will not report' => [false, false, $this->never()],
        ];
    }
}
