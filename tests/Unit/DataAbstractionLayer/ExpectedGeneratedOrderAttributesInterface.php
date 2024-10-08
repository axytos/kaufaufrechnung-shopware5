<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\DataAbstractionLayer;

interface ExpectedGeneratedOrderAttributesInterface
{
    /**
     * @return string
     */
    public function getAxytosKaufAufRechnungCheckProcessState();

    /**
     * @param string $value
     *
     * @return void
     */
    public function setAxytosKaufAufRechnungCheckProcessState($value);

    /**
     * @return bool
     */
    public function getAxytosKaufAufRechnungHasCancelReported();

    /**
     * @param bool $value
     *
     * @return void
     */
    public function setAxytosKaufAufRechnungHasCancelReported($value);

    /**
     * @return bool
     */
    public function getAxytosKaufAufRechnungHasCreateInvoiceReported();

    /**
     * @param bool $value
     *
     * @return void
     */
    public function setAxytosKaufAufRechnungHasCreateInvoiceReported($value);

    /**
     * @return bool
     */
    public function getAxytosKaufAufRechnungHasRefundReported();

    /**
     * @param bool $value
     *
     * @return void
     */
    public function setAxytosKaufAufRechnungHasRefundReported($value);

    /**
     * @return bool
     */
    public function getAxytosKaufAufRechnungHasShippingReported();

    /**
     * @param bool $value
     *
     * @return void
     */
    public function setAxytosKaufAufRechnungHasShippingReported($value);

    /**
     * @return string
     */
    public function getAxytosKaufAufRechnungPrecheckResponse();

    /**
     * @param string $value
     *
     * @return void
     */
    public function setAxytosKaufAufRechnungPrecheckResponse($value);

    /**
     * @return string
     */
    public function getAxytosKaufAufRechnungReportedTrackingCode();

    /**
     * @param string $value
     *
     * @return void
     */
    public function setAxytosKaufAufRechnungReportedTrackingCode($value);

    /**
     * @return string
     */
    public function getAxytosKaufAufRechnungOrderBasketHash();

    /**
     * @param string $value
     *
     * @return void
     */
    public function setAxytosKaufAufRechnungOrderBasketHash($value);
}
