<?php

namespace AxytosKaufAufRechnungShopware5\Configuration;

use Shopware\Models\Order\Status;

class AfterCheckoutOrderStatus
{
    const ORDER_STATE_CANCELLED = 'ORDER_STATE_CANCELLED';
    const ORDER_STATE_OPEN = 'ORDER_STATE_OPEN';
    const ORDER_STATE_IN_PROCESS = 'ORDER_STATE_IN_PROCESS';
    const ORDER_STATE_COMPLETED = 'ORDER_STATE_COMPLETED';
    const ORDER_STATE_PARTIALLY_COMPLETED = 'ORDER_STATE_PARTIALLY_COMPLETED';
    const ORDER_STATE_CANCELLED_REJECTED = 'ORDER_STATE_CANCELLED_REJECTED';
    const ORDER_STATE_READY_FOR_DELIVERY = 'ORDER_STATE_READY_FOR_DELIVERY';
    const ORDER_STATE_PARTIALLY_DELIVERED = 'ORDER_STATE_PARTIALLY_DELIVERED';
    const ORDER_STATE_COMPLETELY_DELIVERED = 'ORDER_STATE_COMPLETELY_DELIVERED';
    const ORDER_STATE_CLARIFICATION_REQUIRED = 'ORDER_STATE_CLARIFICATION_REQUIRED';

    /**
     * @var int
     */
    private static $default = Status::ORDER_STATE_OPEN;

    /**
     * @var array<string,int>
     */
    private static $orderStatusMapping = [
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

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $value = (string) $value;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        if (!isset(self::$orderStatusMapping[$this->value])) {
            return self::$default;
        }

        return self::$orderStatusMapping[$this->value];
    }
}
