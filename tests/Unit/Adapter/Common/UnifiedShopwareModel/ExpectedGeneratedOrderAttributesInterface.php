<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common\UnifiedShopwareModel;

interface ExpectedGeneratedOrderAttributesInterface
{
    /**
     * @return string
     */
    public function getAxytosKaufAufRechnungOrderState();

    /**
     * @param string $value
     *
     * @return void
     */
    public function setAxytosKaufAufRechnungOrderState($value);

    /**
     * @return string
     */
    public function getAxytosKaufAufRechnungOrderStateData();

    /**
     * @param string $value
     *
     * @return void
     */
    public function setAxytosKaufAufRechnungOrderStateData($value);

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
