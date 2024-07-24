<?php

namespace AxytosKaufAufRechnungShopware5\Subscriber\PluginConfigurationValidation;

use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use AxytosKaufAufRechnungShopware5\Paymentmethod\PaymentMethodOptions;
use AxytosKaufAufRechnungShopware5\Configuration\PluginConfigurationValueNames;
use Shopware\Components\Plugin\PaymentInstaller;
use Shopware\Models\Payment\Payment;
use Shopware\Models\Payment\Repository;

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
        $paymentRepository = Shopware()->Models()->getRepository(Payment::class);
        /** @var Payment|null $payment */
        $payment = $paymentRepository->findOneBy([
           'name' => $options['name'],
        ]);
        if (!is_null($payment)) {
            $options['position'] = $payment->getPosition();
            $options['additionalDescription'] = $payment->getAdditionalDescription();
        }
        $options['active'] = intval(!$pluginConfigurationValidator->isInvalid());

        /** @var PaymentInstaller */
        $installer = Shopware()->Container()->get('shopware.plugin_payment_installer');
        $installer->createOrUpdate(PluginConfigurationValueNames::PLUGIN_NAME, $options);
    }
}
