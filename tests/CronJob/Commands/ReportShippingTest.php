<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob\Commands;

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use Axytos\ECommerce\Logging\LoggerAdapterInterface;
use AxytosKaufAufRechnungShopware5\CronJob\Commands\ReportShipping;
use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReportShippingTest extends TestCase
{
    /**
     * @var \Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface&MockObject
     */
    private $invoiceClient;

    /**
     * @var \AxytosKaufAufRechnungShopware5\CronJob\Commands\ReportShipping
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->invoiceClient = $this->createMock(InvoiceClientInterface::class);

        $this->sut = new ReportShipping(
            $this->invoiceClient,
            $this->createMock(LoggerAdapterInterface::class)
        );
    }

    /**
     * @dataProvider execute_cases
     * @param bool $hasShippingReported
     * @param bool $hasBeenShipped
     * @param \PHPUnit\Framework\MockObject\Rule\InvokedCount $reportShippingInvocations
     * @return void
     */
    public function test_execute_reports_shipping($hasShippingReported, $hasBeenShipped, $reportShippingInvocations)
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $shopSystemOrder->method('hasShippingReported')->willReturn($hasShippingReported);
        $shopSystemOrder->method('hasBeenShipped')->willReturn($hasBeenShipped);

        $reportData = $this->createMock(InvoiceOrderContextInterface::class);
        $shopSystemOrder->method('getShippingReportData')->willReturn($reportData);

        $this->invoiceClient->expects($reportShippingInvocations)->method('reportShipping')->with($reportData);

        $this->sut->execute($shopSystemOrder);
    }

    /**
     * @dataProvider execute_cases
     * @param bool $hasShippingReported
     * @param bool $hasBeenShipped
     * @param \PHPUnit\Framework\MockObject\Rule\InvokedCount $reportShippingInvocations
     * @return void
     */
    public function test_execute_saves_shipping_reported($hasShippingReported, $hasBeenShipped, $reportShippingInvocations)
    {
        /** @var ShopSystemOrderInterface&MockObject */
        $shopSystemOrder = $this->createMock(ShopSystemOrderInterface::class);

        $shopSystemOrder->method('hasShippingReported')->willReturn($hasShippingReported);
        $shopSystemOrder->method('hasBeenShipped')->willReturn($hasBeenShipped);

        $shopSystemOrder->expects($reportShippingInvocations)->method('saveHasShippingReported');

        $this->sut->execute($shopSystemOrder);
    }

    /**
     * @return mixed[]
     */
    public function execute_cases()
    {
        return [
            'already reported and shipped     -> will not report' => [true, true, $this->never()],
            'already reported and not shipped -> will not report' => [true, false, $this->never()],
            'not yet reported and shipped     -> will report' => [false, true, $this->once()],
            'not yet reported and not shipped -> will not report' => [false, false, $this->never()],
        ];
    }
}
