<?php

namespace AxytosKaufAufRechnungShopware5\Subscriber\PluginConfigurationValidation;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Shopware\Models\Config\Element;
use Doctrine\ORM\Events;
use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use AxytosKaufAufRechnungShopware5\Paymentmethod\PaymentMethodOptions;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfigurationValueNames;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use Shopware\Components\Plugin\PaymentInstaller;

class PluginConfigurationPostUpdateSubscriber implements EventSubscriber
{
    /**
     * @return mixed[]
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postUpdate
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
            if (!$model instanceof Element) {
                return;
            }

            /** @var PluginConfigurationValidator */
            $pluginConfigurationValidator = Shopware()->Container()->get(PluginConfigurationValidator::class);

            $options = PaymentMethodOptions::OPTIONS;
            $options['active'] = intval(!$pluginConfigurationValidator->isInvalid());

            /** @var PaymentInstaller */
            $installer = Shopware()->Container()->get('shopware.plugin_payment_installer');
            $installer->createOrUpdate(PluginConfigurationValueNames::PLUGIN_NAME, $options);
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
