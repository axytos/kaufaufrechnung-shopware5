<?php

namespace AxytosKaufAufRechnungShopware5\Subscriber;

use Axytos\ECommerce\Clients\Checkout\CheckoutClientInterface;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use AxytosKaufAufRechnungShopware5\Paymentmethod\PaymentMethodOptions;
use Enlight\Event\SubscriberInterface;
use Enlight_View_Default;

class CheckoutSubscriber implements SubscriberInterface
{
    private CheckoutClientInterface $checkoutClient;

    public function __construct(
        CheckoutClientInterface $checkoutClient
    ) {
        $this->checkoutClient = $checkoutClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onPostDispatch',
        ];
    }

    public function onPostDispatch(\Enlight_Controller_ActionEventArgs $args): void
    {
        try {
            /** @var Enlight_View_Default */
            $view = $args->getSubject()->View();

            /** @phpstan-ignore-next-line because of Enlight_View::__get access */
            if ($view->sPayment["name"] == PaymentMethodOptions::NAME) {
                $creditCheckAgreementInfo = $this->checkoutClient->getCreditCheckAgreementInfo();

                /** @phpstan-ignore-next-line because of Enlight_View::__set access */
                $view->creditCheckAgreementInfo = $creditCheckAgreementInfo;
            }
        } catch (\Throwable $th) {
            /** @var ErrorHandler */
            $errorHandler = Shopware()->Container()->get(ErrorHandler::class);
            $errorHandler->handle($th);
        }
    }
}
