<?php

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\PaymentStatus;
use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfiguration;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use Shopware\Components\CSRFWhitelistAware;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

class Shopware_Controllers_Frontend_AxytosKaufAufRechnungPaymentCallback extends Enlight_Controller_Action implements CSRFWhitelistAware
{
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
     * @var \Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface
     */
    private $invoiceClient;
    /**
     * @var \Shopware\Components\Model\ModelManager
     */
    private $entityManager;

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
        $this->invoiceClient = Shopware()->Container()->get(InvoiceClientInterface::class);
        /** @phpstan-ignore-next-line */
        $this->entityManager = Shopware()->Container()->get('models');
    }

    /**
     * @return void
     */
    public function paymentAction()
    {
        try {
            if ($this->isNotPostRequest()) {
                $this->setResponseStatusCode(401);
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
     * @param int $statusCode
     * @return void
     */
    private function setResponseStatusCode($statusCode)
    {
        if (!method_exists($this->response, 'setStatusCode')) {
            return;
        }
        $this->response->setStatusCode($statusCode);
    }

    /**
     * @return bool
     */
    private function isClientSecretInvalid()
    {
        $configClientSecret = $this->pluginConfiguration->getClientSecret();

        $headerClientSecret = $this->request->getHeader("X-secret");

        return is_null($configClientSecret) || $configClientSecret !== $headerClientSecret;
    }

    /**
     * @return void
     * @param string $paymentId
     */
    private function setOrderState($paymentId)
    {
        $paymentId = (string) $paymentId;
        $invoiceOrderPaymentUpdate = $this->invoiceClient->getInvoiceOrderPaymentUpdate($paymentId);

        switch ($invoiceOrderPaymentUpdate->paymentStatus) {
            case PaymentStatus::PAID:
            case PaymentStatus::OVERPAID:
                $this->setOrderStatusPaid($invoiceOrderPaymentUpdate->orderId);
                return;
            default:
                return;
        }
    }

    /**
     * @return void
     * @param string $orderNumber
     */
    private function setOrderStatusPaid($orderNumber)
    {
        $orderNumber = (string) $orderNumber;
        /** @var Order */
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['number' => $orderNumber]);

        /** @var Status */
        $status = $this->entityManager->find(Status::class, Status::PAYMENT_STATE_COMPLETELY_PAID);

        $order->setPaymentStatus($status);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
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
     * @return bool
     */
    private function isNotPostRequest()
    {
        $requestMethod = strtolower($this->request->getMethod());
        return $requestMethod !== 'post';
    }

    /**
     * @return mixed[]
     */
    public function getWhitelistedCSRFActions()
    {
        return [
            'payment'
        ];
    }
}
