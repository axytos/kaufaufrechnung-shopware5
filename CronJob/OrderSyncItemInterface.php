<?php

namespace AxytosKaufAufRechnungShopware5\CronJob;

interface OrderSyncItemInterface
{
    /**
     * @return void
     */
    public function reportCancel();

    /**
     * @return void
     */
    public function reportCreateInvoice();

    /**
     * @return void
     */
    public function reportRefund();

    /**
     * @return void
     */
    public function reportShipping();

    /**
     * @return void
     */
    public function reportTrackingInformation();

    /**
     * @param OrderSyncCommandInterface $command
     * @return void
     */
    public function execute($command);
}
