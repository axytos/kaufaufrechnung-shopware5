<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Subscriber;

use Axytos\ECommerce\Clients\Invoice\InvoiceClient;
use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use Axytos\ECommerce\Order\OrderCheckProcessStates;
use AxytosKaufAufRechnungShopware5\Core\InvoiceOrderContextFactory;
use AxytosKaufAufRechnungShopware5\Core\OrderCheckProcessStateMachine;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use AxytosKaufAufRechnungShopware5\Paymentmethod\PaymentMethodOptions;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

class ShipOrderSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate
        ];
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
        try {
            /** @var PluginConfigurationValidator */
            $pluginConfigurationValidator = Shopware()->Container()->get(PluginConfigurationValidator::class);
            if ($pluginConfigurationValidator->isInvalid()) {
                return;
            }

            $entity = $eventArgs->getEntity();

            if (!($entity instanceof Order)) {
                return;
            }

            if ($entity->getPayment()->getName() !== PaymentMethodOptions::NAME) {
                return;
            }

            if (!$eventArgs->hasChangedField('orderStatus')) {
                return;
            }

            /** @var Status */
            $newOrderStatus = $eventArgs->getNewValue('orderStatus');
            if ($newOrderStatus->getId() !== Status::ORDER_STATE_COMPLETELY_DELIVERED) {
                return;
            }

            /** @var OrderCheckProcessStateMachine */
            $orderCheckProcessStateMachine = Shopware()->Container()->get(OrderCheckProcessStateMachine::class);
            $orderState = $orderCheckProcessStateMachine->getState($entity);

            if ($orderState !== OrderCheckProcessStates::CONFIRMED) {
                return;
            }

            /** @var InvoiceOrderContextFactory */
            $invoiceOrderContextFactory = Shopware()->Container()->get(InvoiceOrderContextFactory::class);
            $invoiceOrderContext = $invoiceOrderContextFactory->create($entity);

            /** @var InvoiceClient */
            $invoiceClient = Shopware()->Container()->get(InvoiceClientInterface::class);
            $invoiceClient->reportShipping($invoiceOrderContext);
        } catch (\Throwable $th) {
            /** @var ErrorHandler */
            $errorHandler = Shopware()->Container()->get(ErrorHandler::class);
            $errorHandler->handle($th);
        }
    }
}
