<?php

namespace AxytosKaufAufRechnungShopware5\Subscriber;

use Axytos\ECommerce\Clients\Invoice\InvoiceClientInterface;
use Axytos\ECommerce\Clients\Invoice\InvoiceOrderContextInterface;
use Enlight\Event\SubscriberInterface;
use Enlight_Event_EventArgs;

class CreateInvoiceSubscriber implements SubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            CustomEventNames::CREATE_INVOICE => 'onCreateInvoice',
        );
    }

    private InvoiceClientInterface $invoiceClient;

    public function __construct(InvoiceClientInterface $invoiceClient)
    {
        $this->invoiceClient = $invoiceClient;
    }

    public function onCreateInvoice(Enlight_Event_EventArgs $args): void
    {
        /** @var InvoiceOrderContextInterface */
        $invoiceOrderContext = $args->get('AxytosInvoiceOrderContext');

        $this->invoiceClient->createInvoice($invoiceOrderContext);
    }
}
