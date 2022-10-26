<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Core;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

class OrderStateMachine
{
    /** @var ModelManager */
    private $modelManager;

    public function __construct(
        ModelManager $modelManager
    ) {
        $this->modelManager = $modelManager;
    }

    public function setCanceled(Order $order): void
    {
        $this->setState($order, Status::ORDER_STATE_CANCELLED);
    }

    public function setPaymentReview(Order $order): void
    {
        $this->setState($order, Status::PAYMENT_STATE_REVIEW_NECESSARY);
    }

    public function setPendingPayment(Order $order): void
    {
        $this->setState($order, Status::PAYMENT_STATE_OPEN);
    }

    public function setTechnicalError(Order $order): void
    {
        $this->setState($order, Status::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED);
    }

    public function setComplete(Order $order): void
    {
        $this->setState($order, Status::ORDER_STATE_COMPLETED);
    }

    private function setState(Order $order, int $status): void
    {
        $statusRepo = $this->modelManager->getRepository(Status::class);
        /** @var Status */
        $status = $statusRepo->find($status);

        if ($status->getGroup() == Status::GROUP_PAYMENT) {
            $order->setPaymentStatus($status);
        } elseif ($status == Status::GROUP_STATE) {
            $order->setOrderStatus($status);
        }

        $this->modelManager->persist($order);
        $this->modelManager->flush();
    }
}
