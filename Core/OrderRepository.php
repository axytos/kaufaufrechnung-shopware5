<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Core;

use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutOrderStatus;
use AxytosKaufAufRechnungShopware5\Configuration\AfterCheckoutPaymentStatus;
use Doctrine\ORM\EntityRepository;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

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

    public function saveOrderStatus(Order $order, int $orderStatusCode): void
    {
        $orderStatus = $this->findStatusModelByCode($orderStatusCode);

        $order->setOrderStatus($orderStatus);

        $this->saveOrder($order);
    }

    public function savePaymentStatus(Order $order, int $paymentStatusCode): void
    {
        $paymentStatus = $this->findStatusModelByCode($paymentStatusCode);

        $order->setPaymentStatus($paymentStatus);

        $this->saveOrder($order);
    }

    public function saveAfterCheckoutOrderStatus(Order $order, AfterCheckoutOrderStatus $afterCheckoutOrderStatus): void
    {
        $this->saveOrderStatus($order, $afterCheckoutOrderStatus->getStatusCode());
    }

    public function saveAfterCheckoutPaymentStatus(Order $order, AfterCheckoutPaymentStatus $afterCheckoutPaymentStatus): void
    {
        $this->savePaymentStatus($order, $afterCheckoutPaymentStatus->getStatusCode());
    }

    public function saveOrder(Order $order): void
    {
        /** @var ModelManager */
        $modelManager = Shopware()->Container()->get(ModelManager::class);

        $modelManager->persist($order);
        $modelManager->flush();
    }

    private function findStatusModelByCode(int $statusCode): Status
    {
        /** @var ModelManager */
        $modelManager = Shopware()->Container()->get(ModelManager::class);

        /** @var EntityRepository */
        $statusRepository = $modelManager->getRepository(Status::class);

        /** @var Status */
        return $statusRepository->find($statusCode);
    }
}
