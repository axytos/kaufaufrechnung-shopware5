<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Subscriber;

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;

class RefundOrderSubscriber implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            CustomEventNames::REFUND_ORDER => 'onRefundOrder',
        );
    }

    private InvoiceClientInterface $invoiceClient;

    public function __construct(InvoiceClientInterface $invoiceClient)
    {
        $this->invoiceClient = $invoiceClient;
    }

    public function onRefundOrder(Enlight_Event_EventArgs $args): void
    {
        /** @var InvoiceOrderContextInterface */
        $invoiceOrderContext = $args->get('AxytosInvoiceOrderContext');

        $this->invoiceClient->refund($invoiceOrderContext);
    }
}
