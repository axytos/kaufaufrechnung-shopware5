<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Core;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;

class OrderRepository
{
    public function findOrder(int $orderId): ?Order
    {
        /** @var ModelManager */
        $modelManager = Shopware()->Container()->get(ModelManager::class);

        /** @var \Shopware\Models\Order\Repository */
        $orderRepository = $modelManager->getRepository(Order::class);

        return $orderRepository->find($orderId);
    }
}
