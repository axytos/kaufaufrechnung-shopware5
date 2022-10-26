<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Subscriber;

abstract class CustomEventNames
{
    public const CREATE_INVOICE = 'Axytos_KaufAufRechnung_CreateInvoice';
    public const REFUND_ORDER = 'Axytos_KaufAufRechnung_RefundOrder';
}
