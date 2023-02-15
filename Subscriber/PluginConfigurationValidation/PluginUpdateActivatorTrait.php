<?php

namespace AxytosKaufAufRechnungShopware5\Subscriber\PluginConfigurationValidation;

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use AxytosKaufAufRechnungShopware5\Paymentmethod\PaymentMethodOptions;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfigurationValueNames;
use Shopware\Components\Plugin\PaymentInstaller;

trait PluginUpdateActivatorTrait
{
    /**
     * @return void
     */
    public function updatePluginActivationState()
    {
        /** @var PluginConfigurationValidator */
        $pluginConfigurationValidator = Shopware()->Container()->get(PluginConfigurationValidator::class);

        $options = PaymentMethodOptions::OPTIONS;
        $options['active'] = intval(!$pluginConfigurationValidator->isInvalid());

        /** @var PaymentInstaller */
        $installer = Shopware()->Container()->get('shopware.plugin_payment_installer');
        $installer->createOrUpdate(PluginConfigurationValueNames::PLUGIN_NAME, $options);
    }
}
