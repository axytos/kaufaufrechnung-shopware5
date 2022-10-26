<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Subscriber;

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use Axytos\ECommerce\Order\OrderCheckProcessStates;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use AxytosKaufAufRechnungShopware5\Core\OrderCheckProcessStateMachine;
use AxytosKaufAufRechnungShopware5\Core\OrderDocumentsRepository;
use AxytosKaufAufRechnungShopware5\Core\OrderRepository;
use AxytosKaufAufRechnungShopware5\Paymentmethod\PaymentMethodOptions;
use Enlight\Event\SubscriberInterface;
use Enlight_Controller_Request_RequestHttp;
use Enlight_Event_EventArgs;
use Shopware\Models\Order\Document\Document;
use Shopware\Models\Order\Order;

class BackendOrderCreateDocumentSubscriber implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            'Enlight_Controller_Action_PostDispatch_Backend_Order' => 'onPostDispatchBackendOrder',
        );
    }

    private OrderRepository $orderRepository;
    private OrderDocumentsRepository $orderDocumentsRepository;
    private CustomEventDispatcher $customEventDispatcher;
    private PluginConfigurationValidator $pluginConfigurationValidator;
    private OrderCheckProcessStateMachine $orderCheckProcessStateMachine;
    private ErrorHandler $errorHandler;

    public function __construct(
        OrderRepository $orderRepository,
        OrderDocumentsRepository $orderDocumentsRepository,
        CustomEventDispatcher $customEventDispatcher,
        PluginConfigurationValidator $pluginConfigurationValidator,
        OrderCheckProcessStateMachine $orderCheckProcessStateMachine,
        ErrorHandler $errorHandler
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderDocumentsRepository = $orderDocumentsRepository;
        $this->customEventDispatcher = $customEventDispatcher;
        $this->pluginConfigurationValidator = $pluginConfigurationValidator;
        $this->orderCheckProcessStateMachine = $orderCheckProcessStateMachine;
        $this->errorHandler = $errorHandler;
    }

    public function onPostDispatchBackendOrder(Enlight_Event_EventArgs $args): void
    {
        try {
            if ($this->pluginConfigurationValidator->isInvalid()) {
                return;
            }

            /**
             * @var Enlight_Controller_Request_RequestHttp
             * @phpstan-ignore-next-line
             */
            $request = $args->getRequest();

            if ($request->getActionName() !== 'createDocument') {
                return;
            }

            $orderId = $request->getParam('orderId');
            $documentType = $request->getParam('documentType');

            /** @var Order */
            $order = $this->orderRepository->findOrder($orderId);

            /** @var Document */
            $document = $this->orderDocumentsRepository->findOrderDocumentWithType($order, $documentType);

            $this->dispatchCustomEvent($order, $document);
        } catch (\Throwable $th) {
            $this->errorHandler->handle($th);
        }
    }

    private function dispatchCustomEvent(Order $order, Document $document): void
    {
        if ($this->isNotAxytosConfirmedOrder($order)) {
            return;
        }

        switch ($document->getType()->getKey()) {
            case 'invoice':
                $this->customEventDispatcher->dispatchCreateInvoice($order, $document);
                break;

            case 'delivery_note':
                break;

            case 'credit':
                $this->customEventDispatcher->dispatchRefundOrder($order, $document);
                break;

            case 'cancellation':
                break;

            default:
                break;
        }
    }

    private function isNotAxytosConfirmedOrder(Order $order): bool
    {
        /** @phpstan-ignore-next-line */
        if ($order->getPayment()->getName() != PaymentMethodOptions::NAME) {
            return true;
        }

        $orderState = $this->orderCheckProcessStateMachine->getState($order);

        if ($orderState !== OrderCheckProcessStates::CONFIRMED) {
            return true;
        }

        return false;
    }
}
