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
    private ErrorHandler $errorHandler;
    private PluginConfigurationValidator $pluginConfigurationValidator;
    private PluginConfiguration $pluginConfiguration;
    private InvoiceClientInterface $invoiceClient;
    private ModelManager $entityManager;

    public function preDispatch()
    {
        $this->errorHandler = $this->get(ErrorHandler::class);
        $this->pluginConfigurationValidator = $this->get(PluginConfigurationValidator::class);
        $this->pluginConfiguration = $this->get(PluginConfiguration::class);
        $this->invoiceClient = $this->get(InvoiceClientInterface::class);
        $this->entityManager = $this->get(ModelManager::class);
    }

    public function paymentAction(): void
    {
        try {
            if ($this->isNotPostRequest()) {
                $this->response->setStatusCode(401);
                return;
            }

            if ($this->pluginConfigurationValidator->isInvalid()) {
                $this->response->setStatusCode(500);
                return;
            }

            if ($this->isClientSecretInvalid()) {
                $this->response->setStatusCode(401);
                return;
            }

            $paymentId = $this->getRequestPaymentId();
            $this->setOrderState($paymentId);
        } catch (\Throwable $th) {
            $this->response->setStatusCode(500);
            $this->errorHandler->handle($th);
        }
    }

    private function isClientSecretInvalid(): bool
    {
        $configClientSecret = $this->pluginConfiguration->getClientSecret();

        $headerClientSecret = $this->request->headers->get("X-secret");

        return is_null($configClientSecret) || $configClientSecret !== $headerClientSecret;
    }

    private function setOrderState(string $paymentId): void
    {
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

    private function setOrderStatusPaid(string $orderNumber): void
    {
        /** @var Order */
        $order = $this->entityManager->getRepository(Order::class)->findOneBy(['number' => $orderNumber]);

        /** @var Status */
        $status = $this->entityManager->find(Status::class, Status::PAYMENT_STATE_COMPLETELY_PAID);

        $order->setPaymentStatus($status);

        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    private function getRequestPaymentId(): string
    {
        $pathData = explode("/", $this->request->getPathInfo());
        return end($pathData);
    }

    private function isNotPostRequest(): bool
    {
        $requestMethod = strtolower($this->request->getMethod());
        return $requestMethod !== 'post';
    }

    public function getWhitelistedCSRFActions(): array
    {
        return [
            'payment'
        ];
    }
}
