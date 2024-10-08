<?php

namespace AxytosKaufAufRechnungShopware5\Subscriber\PluginConfigurationValidation;

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Shopware\Models\Payment\Payment;

class PaymentMethodConfigurationPreUpdateSubscriber implements EventSubscriber
{
    /**
     * @return array<string>
     */
    public function getSubscribedEvents()
    {
        return [
            Events::preUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     *
     * @return void
     */
    public function preUpdate($eventArgs)
    {
        try {
            $model = $eventArgs->getEntity();

            if (!$model instanceof Payment) {
                return;
            }

            if ('axytos_kauf_auf_rechnung' !== $model->getName()) {
                return;
            }

            /** @var PluginConfigurationValidator */
            $pluginConfigurationValidator = Shopware()->Container()->get(PluginConfigurationValidator::class);

            $model->setActive($model->getActive() && !$pluginConfigurationValidator->isInvalid());
            $model->setEsdActive($model->getEsdActive() && !$pluginConfigurationValidator->isInvalid());
            $model->setMobileInactive($model->getMobileInactive() && !$pluginConfigurationValidator->isInvalid());
        } catch (\Throwable $th) {
            /** @var ErrorHandler */
            $errorHandler = Shopware()->Container()->get(ErrorHandler::class);
            $errorHandler->handle($th);
        } catch (\Exception $th) { // @phpstan-ignore-line | php5.6 compatibility
            /** @var ErrorHandler */
            $errorHandler = Shopware()->Container()->get(ErrorHandler::class);
            $errorHandler->handle($th);
        }
    }
}
