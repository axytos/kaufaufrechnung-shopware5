<?php

namespace AxytosKaufAufRechnungShopware5\Tests\CronJob\IntegratedTests\Mocks;

use AxytosKaufAufRechnungShopware5\CronJob\ShopSystemOrderInterface;

class ShopSystemOrderMock implements ShopSystemOrderInterface
{
    /**
     * @var string|int|null
     */
    private $orderNumber;

    /**
     * @var array<string,array<string,bool>>
     */
    private $config;

    /**
     * @param string|int|null $orderNumber
     * @param array<string,array<string,bool>> $config
     */
    public function __construct($orderNumber, $config)
    {
        $this->orderNumber = $orderNumber;
        $this->config = $config;
        $this->config['actual'] = [
            'saveHasCancelReported' => false,
            'saveHasCreateInvoiceReported' => false,
            'saveHasRefundReported' => false,
            'saveHasShippingReported' => false,
            'saveNewTrackingInformation' => false,
        ];
    }

    /**
     * @return array<string,array<string,bool>>
     */
    public function getTestConfig()
    {
        return $this->config;
    }

    /**
     * @return string|int|null
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    //==================================================================================
    // Transaction
    //==================================================================================

    /**
     * @return void
     */
    public function beginPersistenceTransaction()
    {
    }

    /**
     * @return void
     */
    public function commitPersistenceTransaction()
    {
    }

    /**
     * @return void
     */
    public function rollbackPersistenceTransaction()
    {
    }

    //==================================================================================
    // CreateInvoice
    //==================================================================================

    /**
     * @return bool
     */
    public function hasCreateInvoiceReported()
    {
        return $this->config['order']['hasCreateInvoiceReported'];
    }
    /**
     * @return void
     */
    public function saveHasCreateInvoiceReported()
    {
        $this->config['actual']['saveHasCreateInvoiceReported'] = true;
    }
    /**
     * @return bool
     */
    public function hasBeenInvoiced()
    {
        return $this->config['order']['hasBeenInvoiced'];
    }
    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getCreateInvoiceReportData()
    {
        return new InvoiceOrderContextMock($this);
    }


    //==================================================================================
    // Cancel
    //==================================================================================

    /**
     * @return bool
     */
    public function hasCancelReported()
    {
        return $this->config['order']['hasCancelReported'];
    }
    /**
     * @return void
     */
    public function saveHasCancelReported()
    {
        $this->config['actual']['saveHasCancelReported'] = true;
    }
    /**
     * @return bool
     */
    public function hasBeenCanceled()
    {
        return $this->config['order']['hasBeenCanceled'];
    }
    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getCancelReportData()
    {
        return new InvoiceOrderContextMock($this);
    }

    //==================================================================================
    // Refund
    //==================================================================================

    /**
     * @return bool
     */
    public function hasRefundReported()
    {
        return $this->config['order']['hasRefundReported'];
    }
    /**
     * @return void
     */
    public function saveHasRefundReported()
    {
        $this->config['actual']['saveHasRefundReported'] = true;
    }
    /**
     * @return bool
     */
    public function hasBeenRefunded()
    {
        return $this->config['order']['hasBeenRefunded'];
    }
    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getRefundReportData()
    {
        return new InvoiceOrderContextMock($this);
    }

    //==================================================================================
    // Shipping
    //==================================================================================

    /**
     * @return bool
     */
    public function hasShippingReported()
    {
        return $this->config['order']['hasShippingReported'];
    }
    /**
     * @return void
     */
    public function saveHasShippingReported()
    {
        $this->config['actual']['saveHasShippingReported'] = true;
    }
    /**
     * @return bool
     */
    public function hasBeenShipped()
    {
        return $this->config['order']['hasBeenShipped'];
    }
    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getShippingReportData()
    {
        return new InvoiceOrderContextMock($this);
    }

    //==================================================================================
    // Tracking Information
    //==================================================================================

    /**
     * @return bool
     */
    public function hasNewTrackingInformation()
    {
        return $this->config['order']['hasNewTrackingInformation'];
    }
    /**
     * @return void
     */
    public function saveNewTrackingInformation()
    {
        $this->config['actual']['saveNewTrackingInformation'] = true;
    }
    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getNewTrackingInformationReportData()
    {
        return new InvoiceOrderContextMock($this);
    }
}
