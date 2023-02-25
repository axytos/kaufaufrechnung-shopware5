<?php

namespace AxytosKaufAufRechnungShopware5\Subscriber\PluginConfigurationValidation;

use Axytos\ECommerce\Abstractions\ApiHostProviderInterface;
use Axytos\ECommerce\Abstractions\ApiKeyProviderInterface;
use Axytos\ECommerce\Clients\Invoice\PluginConfigurationValidator;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\DispatchRepository;
use AxytosKaufAufRechnungShopware5\Paymentmethod\PaymentMethodOptions;

class PluginConfigurationValidatorDecorator extends PluginConfigurationValidator
{
    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\DispatchRepository
     */
    private $dispatchRepository;

    public function __construct(
        ApiHostProviderInterface $apiHostProvider,
        ApiKeyProviderInterface $apiKeyProvider,
        DispatchRepository $dispatchRepository
    ) {
        parent::__construct($apiHostProvider, $apiKeyProvider);
        $this->dispatchRepository = $dispatchRepository;
    }

    /**
     * @return bool
     */
    public function isInvalid()
    {
        try {
            return parent::isInvalid()
                || !$this->isReferencedByDispatch();
        } catch (\Throwable $th) {
            return true;
        } catch (\Exception $th) { // @phpstan-ignore-line / php5 compatibility
            return true;
        }
    }

    /**
     * @return bool
     */
    private function isReferencedByDispatch()
    {
        $options = PaymentMethodOptions::OPTIONS;
        $pluginName = $options["name"];

        $dispatches = $this->dispatchRepository->findAll();
        foreach ($dispatches as $dispatch) {
            $payments = $dispatch->getPayments();

            foreach ($payments as $payment) {
                if ($payment->getName() == $pluginName) {
                    return true;
                }
            }
        }

        return false;
    }
}
