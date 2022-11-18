<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Paymentmethod;

abstract class PaymentMethodOptions
{
    public const OPTIONS = [
        'name' => 'axytos_kauf_auf_rechnung',
        'description' => 'Kauf auf Rechnung',
        'action' => 'AxytosKaufAufRechnungController',
        'active' => 1,
        'position' => 0,
        'additionalDescription' => '<div id="payment_desc">Sie zahlen bequem die Rechnung, sobald Sie die Ware erhalten haben, innerhalb der Zahlfrist</div>'
    ];

    public const NAME = PaymentMethodOptions::OPTIONS["name"];
}
