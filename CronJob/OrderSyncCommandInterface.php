<?php

namespace AxytosKaufAufRechnungShopware5\CronJob;

interface OrderSyncCommandInterface
{
    /**
     * @param ShopSystemOrderInterface $shopSystemOrder
     * @return void
     */
    public function execute($shopSystemOrder);
}
