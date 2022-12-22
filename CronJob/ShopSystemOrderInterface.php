<?php

namespace AxytosKaufAufRechnungShopware5\CronJob;

interface ShopSystemOrderInterface
{
    /**
     * @return string|int|null
     */
    public function getOrderNumber();

    //==================================================================================
    // Transaction
    //==================================================================================

    /**
     * @return void
     */
    public function beginPersistenceTransaction();
    /**
     * @return void
     */
    public function commitPersistenceTransaction();
    /**
     * @return void
     */
    public function rollbackPersistenceTransaction();

    //==================================================================================
    // CreateInvoice
    //==================================================================================

    /**
     * @return bool
     */
    public function hasCreateInvoiceReported();
    /**
     * @return void
     */
    public function saveHasCreateInvoiceReported();
    /**
     * @return bool
     */
    public function hasBeenInvoiced();
    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getCreateInvoiceReportData();


    //==================================================================================
    // Cancel
    //==================================================================================

    /**
     * @return bool
     */
    public function hasCancelReported();
    /**
     * @return void
     */
    public function saveHasCancelReported();
    /**
     * @return bool
     */
    public function hasBeenCanceled();
    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getCancelReportData();

    //==================================================================================
    // Refund
    //==================================================================================

    /**
     * @return bool
     */
    public function hasRefundReported();
    /**
     * @return void
     */
    public function saveHasRefundReported();
    /**
     * @return bool
     */
    public function hasBeenRefunded();
    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getRefundReportData();

    //==================================================================================
    // Shipping
    //==================================================================================

    /**
     * @return bool
     */
    public function hasShippingReported();
    /**
     * @return void
     */
    public function saveHasShippingReported();
    /**
     * @return bool
     */
    public function hasBeenShipped();
    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getShippingReportData();

    //==================================================================================
    // Tracking Information
    //==================================================================================

    /**
     * @return bool
     */
    public function hasNewTrackingInformation();
    /**
     * @return void
     */
    public function saveNewTrackingInformation();
    /**
     * @return \Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface
     */
    public function getNewTrackingInformationReportData();
}
