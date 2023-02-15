<?php

namespace AxytosKaufAufRechnungShopware5\Subscriber\PluginConfigurationValidation;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use Shopware\Models\Dispatch\Dispatch;

class DispatchPostUpdateSubscriber implements EventSubscriber
{
    use PluginUpdateActivatorTrait;

    /**
     * @return mixed[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     * @return void
     */
    public function postUpdate($eventArgs)
    {
        try {
            $model = $eventArgs->getEntity();
            if (!$model instanceof Dispatch) {
                return;
            }

            $this->updatePluginActivationState();
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
