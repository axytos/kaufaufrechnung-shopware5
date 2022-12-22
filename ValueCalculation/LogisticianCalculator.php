<?php

namespace AxytosKaufAufRechnungShopware5\ValueCalculation;

use Shopware\Models\Dispatch\Dispatch;
use Shopware\Models\Order\Order;

class LogisticianCalculator
{
    /**
     * @param \Shopware\Models\Order\Order $order
     * @return string
     */
    public function calculate($order)
    {
        /** @var Dispatch */
        $dispatch = $order->getDispatch();
        return $dispatch->getName();
    }
}
