<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob\Commands;

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use AxytosKaufAufRechnungShopware5\CronJob\Commands\ReportTrackingInformation;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReportTrackingInformationTest extends TestCase
{
    /**
     * @var \Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface&MockObject
     */
    private $invoiceClient;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\Commands\ReportTrackingInformation
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->invoiceClient = $this->createMock(InvoiceClientInterface::class);

        $this->sut = new ReportTrackingInformation(
            $this->invoiceClient,
            $this->createMock(LoggerAdapterInterface::class)
        );
    }

    /**
     * @dataProvider execute_cases
     * @param bool $hasNewTrackingInformation
     * @param \PHPUnit\Framework\MockObject\Rule\InvokedCount $reportNewTrackingInformationInvocations
     * @return void
     */
    public function test_execute_reports_new_tracking_information($hasNewTrackingInformation, $reportNewTrackingInformationInvocations)
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $shopSystemOrder->method('hasNewTrackingInformation')->willReturn($hasNewTrackingInformation);

        $reportData = $this->createMock(InvoiceOrderContextInterface::class);
        $shopSystemOrder->method('getNewTrackingInformationReportData')->willReturn($reportData);

        $this->invoiceClient->expects($reportNewTrackingInformationInvocations)->method('trackingInformation')->with($reportData);

        $this->sut->execute($shopSystemOrder);
    }

    /**
     * @dataProvider execute_cases
     * @param bool $hasNewTrackingInformation
     * @param \PHPUnit\Framework\MockObject\Rule\InvokedCount $reportNewTrackingInformationInvocations
     * @return void
     */
    public function test_execute_saves_reported_new_tracking_information($hasNewTrackingInformation, $reportNewTrackingInformationInvocations)
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $shopSystemOrder->method('hasNewTrackingInformation')->willReturn($hasNewTrackingInformation);

        $shopSystemOrder->expects($reportNewTrackingInformationInvocations)->method('saveNewTrackingInformation');

        $this->sut->execute($shopSystemOrder);
    }

    /**
     * @return mixed[]
     */
    public function execute_cases()
    {
        return [
            'new tracking information -> will report' => [true, $this->once()],
            'not new tracking information -> will not report' => [false, $this->never()],
        ];
    }
}
