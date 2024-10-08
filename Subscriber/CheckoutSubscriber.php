<?php

namespace AxytosKaufAufRechnungShopware5\Subscriber;

use Axytos\ECommerce\Clients\Checkout\CheckoutClientInterface;
use AxytosKaufAufRechnungShopware5\ErrorReporting\ErrorHandler;
use AxytosKaufAufRechnungShopware5\Paymentmethod\PaymentMethodOptions;
use Enlight\Event\SubscriberInterface;

class CheckoutSubscriber implements SubscriberInterface
{
    /**
     * @var CheckoutClientInterface
     */
    private $checkoutClient;

    public function __construct(
        CheckoutClientInterface $checkoutClient
    ) {
        $this->checkoutClient = $checkoutClient;
    }

    /**
     * @return array<string, string|array{0:string, 1?: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents()
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Checkout' => 'onPostDispatch',
        ];
    }

    /**
     * @param \Enlight_Controller_ActionEventArgs $args
     *
     * @return void
     */
    public function onPostDispatch($args)
    {
        try {
            /** @var \Enlight_View_Default */
            $view = $args->getSubject()->View();

            /** @phpstan-ignore-next-line because of Enlight_View::__get access */
            if (PaymentMethodOptions::NAME === $view->sPayment['name']) {
                $creditCheckAgreementInfo = $this->checkoutClient->getCreditCheckAgreementInfo();

                /** @phpstan-ignore-next-line because of Enlight_View::__set access */
                $view->creditCheckAgreementInfo = $creditCheckAgreementInfo;
            }
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
