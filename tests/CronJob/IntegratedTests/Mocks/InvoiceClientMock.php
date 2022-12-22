<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob\IntegratedTests\Mocks;

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceOrderPaymentUpdate;

class InvoiceClientMock implements InvoiceClientInterface
{
    /**
     * @var array<string,InvoiceOrderContextInterface[]>
     */
    private $callRecords = [];

    public function __construct()
    {
        $this->callRecords = [
            'cancelOrder' => [],
            'createInvoice' => [],
            'refund' => [],
            'reportShipping' => [],
            'trackingInformation' => [],
        ];
    }

    /**
     * @return array<string,InvoiceOrderContextInterface[]>
     */
    public function getCallRecords()
    {
        return $this->callRecords;
    }

    /**
     * @param \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface $orderContext
     * @return string
     */
    public function precheck($orderContext)
    {
        return '';
    }

    /**
     * @param \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface $orderContext
     * @return void
     */
    public function confirmOrder($orderContext)
    {
    }

    /**
     * @param \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface $orderContext
     * @return void
     */
    public function cancelOrder($orderContext)
    {
        $this->callRecords['cancelOrder'][] = $orderContext;
    }

    /**
     * @param \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface $orderContext
     * @return void
     */
    public function createInvoice($orderContext)
    {
        $this->callRecords['cancelOrder'][] = $orderContext;
    }

    /**
     * @param \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface $orderContext
     * @return void
     */
    public function reportShipping($orderContext)
    {
        $this->callRecords['reportShipping'][] = $orderContext;
    }

    /**
     * @param \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface $orderContext
     * @return void
     */
    public function trackingInformation($orderContext)
    {
        $this->callRecords['trackingInformation'][] = $orderContext;
    }

    /**
     * @param \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface $orderContext
     * @return void
     */
    public function refund($orderContext)
    {
        $this->callRecords['refund'][] = $orderContext;
    }

    /**
     * @param \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface $orderContext
     * @return void
     */
    public function returnOrder($orderContext)
    {
    }

    /**
     * @param string $paymentId
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderPaymentUpdate
     */
    public function getInvoiceOrderPaymentUpdate($paymentId)
    {
        return new InvoiceOrderPaymentUpdate();
    }
}
