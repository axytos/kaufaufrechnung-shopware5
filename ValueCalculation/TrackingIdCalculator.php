<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Order\Order;

class TrackingIdCalculator
{
    /**
     * @param \Shopware\Models\Order\Order $order
     * @return string[]
     */
    public function calculate($order)
    {
        $trackingCode = $order->getTrackingCode();

        if ($trackingCode != "") {
            return [$trackingCode];
        }

        return [];
    }
}
