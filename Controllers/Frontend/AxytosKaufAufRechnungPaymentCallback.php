<?php

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use Axytos\KaufAufRechnung\Core\PaymentStatusUpdateWorker;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\Controllers\AxytosControllerTrait;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_AxytosKaufAufRechnungPaymentCallback extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    use AxytosControllerTrait;

    /**
     * @var \AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler
     */
    private $errorHandler;

    /**
     * @var \Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator
     */
    private $pluginConfigurationValidator;

    /**
     * @var \AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration
     */
    private $pluginConfiguration;

    /**
     * @var \Axytos\KaufAufRechnung\Core\PaymentStatusUpdateWorker
     */
    private $paymentStatusUpdateWorker;


    /**
     * @return void
     */
    public function preDispatch()
    {
        /** @phpstan-ignore-next-line */
        $this->errorHandler = Shopware()->Container()->get(ErrorHandler::class);
        /** @phpstan-ignore-next-line */
        $this->pluginConfigurationValidator = Shopware()->Container()->get(PluginConfigurationValidator::class);
        /** @phpstan-ignore-next-line */
        $this->pluginConfiguration = Shopware()->Container()->get(PluginConfiguration::class);
        /** @phpstan-ignore-next-line */
        $this->paymentStatusUpdateWorker = Shopware()->Container()->get(PaymentStatusUpdateWorker::class);
    }

    /**
     * @return void
     */
    public function paymentAction()
    {
        try {
            if ($this->isNotPostRequest()) {
                $this->setResponseStatusCode(405);
                return;
            }

            if ($this->pluginConfigurationValidator->isInvalid()) {
                $this->setResponseStatusCode(500);
                return;
            }

            if ($this->isClientSecretInvalid()) {
                $this->setResponseStatusCode(401);
                return;
            }

            $paymentId = $this->getRequestPaymentId();
            $this->setOrderState($paymentId);
        } catch (\Throwable $th) {
            $this->setResponseStatusCode(500);
            $this->errorHandler->handle($th);
        } catch (\Exception $th) { // @phpstan-ignore-line | php5.6 compatibility
            $this->setResponseStatusCode(500);
            $this->errorHandler->handle($th);
        }
    }

    /**
     * @return void
     * @param string $paymentId
     */
    private function setOrderState($paymentId)
    {
        $this->paymentStatusUpdateWorker->updatePaymentStatus($paymentId);
    }

    /**
     * @return string
     */
    private function getRequestPaymentId()
    {
        $pathData = explode("/", $this->request->getPathInfo());
        return end($pathData);
    }

    /**
     * @return array<int,string>
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'payment'
        ];
    }

    /**
     * @return bool
     */
    protected function isClientSecretInvalid()
    {
        $configClientSecret = $this->pluginConfiguration->getClientSecret();
        $headerClientSecret = $this->request->getHeader("X-secret");

        return is_null($configClientSecret) || $configClientSecret !== $headerClientSecret;
    }
}
