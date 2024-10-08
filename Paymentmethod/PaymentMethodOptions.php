<?php

namespace AxytosKaufAufRechnungShopware5\Paymentmethod;

abstract class PaymentMethodOptions
{
    const OPTIONS = [
        'name' => 'axytos_kauf_auf_rechnung',
        'description' => 'Kauf auf Rechnung',
        'action' => 'AxytosKaufAufRechnungController',
        'active' => 1,
        'position' => 0,
        'additionalDescription' => 'Sie zahlen bequem die Rechnung, sobald Sie die Ware erhalten haben, innerhalb der Zahlfrist',
    ];

    const NAME = PaymentMethodOptions::OPTIONS['name'];
}
