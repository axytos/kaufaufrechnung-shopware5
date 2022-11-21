<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Configuration;

use Shopware\Models\Order\Status;

class AfterCheckoutOrderStatus
{
    public const ORDER_STATE_CANCELLED = 'ORDER_STATE_CANCELLED';
    public const ORDER_STATE_OPEN = 'ORDER_STATE_OPEN';
    public const ORDER_STATE_IN_PROCESS = 'ORDER_STATE_IN_PROCESS';
    public const ORDER_STATE_COMPLETED = 'ORDER_STATE_COMPLETED';
    public const ORDER_STATE_PARTIALLY_COMPLETED = 'ORDER_STATE_PARTIALLY_COMPLETED';
    public const ORDER_STATE_CANCELLED_REJECTED = 'ORDER_STATE_CANCELLED_REJECTED';
    public const ORDER_STATE_READY_FOR_DELIVERY = 'ORDER_STATE_READY_FOR_DELIVERY';
    public const ORDER_STATE_PARTIALLY_DELIVERED = 'ORDER_STATE_PARTIALLY_DELIVERED';
    public const ORDER_STATE_COMPLETELY_DELIVERED = 'ORDER_STATE_COMPLETELY_DELIVERED';
    public const ORDER_STATE_CLARIFICATION_REQUIRED = 'ORDER_STATE_CLARIFICATION_REQUIRED';

    private static array $orderStatusMapping = [
        self::ORDER_STATE_CANCELLED => Status::ORDER_STATE_CANCELLED,
        self::ORDER_STATE_OPEN => Status::ORDER_STATE_OPEN,
        self::ORDER_STATE_IN_PROCESS => Status::ORDER_STATE_IN_PROCESS,
        self::ORDER_STATE_COMPLETED => Status::ORDER_STATE_COMPLETED,
        self::ORDER_STATE_PARTIALLY_COMPLETED => Status::ORDER_STATE_PARTIALLY_COMPLETED,
        self::ORDER_STATE_CANCELLED_REJECTED => Status::ORDER_STATE_CANCELLED_REJECTED,
        self::ORDER_STATE_READY_FOR_DELIVERY => Status::ORDER_STATE_READY_FOR_DELIVERY,
        self::ORDER_STATE_PARTIALLY_DELIVERED => Status::ORDER_STATE_PARTIALLY_DELIVERED,
        self::ORDER_STATE_COMPLETELY_DELIVERED => Status::ORDER_STATE_COMPLETELY_DELIVERED,
        self::ORDER_STATE_CLARIFICATION_REQUIRED => Status::ORDER_STATE_CLARIFICATION_REQUIRED,
    ];

    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getStatusCode(): int
    {
        return self::$orderStatusMapping[$this->value];
    }
}
