<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations;

use Axytos\ECommerce\Order\OrderCheckProcessStates;
use Axytos\KaufAufRechnung\Core\Model\OrderStateMachine\OrderStates;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\DataLoader;
use Shopware\Bundle\AttributeBundle\Service\DataPersister;
use Shopware\Bundle\AttributeBundle\Service\TableMapping;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

class OrderStateMigration implements MigrationInterface
{
    const ATTRIBUTE_NAME_CHECK_PROCESS_STATE = 'axytos_kauf_auf_rechnung_check_process_state';
    const ATTRIBUTE_NAME_HAS_CANCEL_REPORTED = 'axytos_kauf_auf_rechnung_has_cancel_reported';
    const ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED = 'axytos_kauf_auf_rechnung_has_create_invoice_reported';
    const ATTRIBUTE_NAME_HAS_REFUND_REPORTED = 'axytos_kauf_auf_rechnung_has_refund_reported';

    const ORDER_ATTRIBUTES_TABLE_NAME = 's_order_attributes';

    /**
     * @var TableMapping
     */
    private $tableMapping;

    /**
     * @var DataLoader
     */
    private $dataLoader;

    /**
     * @var DataPersister
     */
    private $dataPersister;

    /**
     * @var CrudService
     */
    private $crudService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var MigrationsRepository
     */
    private $migrationsRepository;

    public function __construct(
        TableMapping $tableMapping,
        DataLoader $dataLoader,
        DataPersister $dataPersister,
        CrudService $crudService,
        ModelManager $modelManager,
        MigrationsRepository $migrationsRepository
    ) {
        $this->tableMapping = $tableMapping;
        $this->dataLoader = $dataLoader;
        $this->dataPersister = $dataPersister;
        $this->crudService = $crudService;
        $this->modelManager = $modelManager;
        $this->migrationsRepository = $migrationsRepository;
    }

    /**
     * @return bool
     */
    public function isMigrationNeeded()
    {
        return $this->tableMapping->isTableColumn(self::ORDER_ATTRIBUTES_TABLE_NAME, self::ATTRIBUTE_NAME_CHECK_PROCESS_STATE);
    }

    /**
     * @return void
     */
    public function migrate()
    {
        $this->migrateColumnData();
        $this->deleteOldColumns();
    }

    /**
     * @return void
     */
    private function migrateColumnData()
    {
        $orderIds = $this->migrationsRepository->getOrderIdsWhereOrderStateMigrationIsNeeded();
        foreach ($orderIds as $orderId) {
            /** @var \Shopware\Models\Order\Repository */
            $orderRepository = $this->modelManager->getRepository(Order::class);
            /** @var Order */
            $order = $orderRepository->find($orderId);

            /** @var array<string,mixed>
             * @phpstan-ignore-next-line */
            $attributes = $this->dataLoader->load(self::ORDER_ATTRIBUTES_TABLE_NAME, $orderId);

            $orderState = $this->mapAttributesToOrderState($order, $attributes);
            $attributes[OrderAttributesRepository::ATTRIBUTE_NAME_ORDER_STATE] = $orderState;
            $this->dataPersister->persist($attributes, self::ORDER_ATTRIBUTES_TABLE_NAME, $orderId);
        }
    }

    /**
     * @param Order               $order
     * @param array<string,mixed> $attributes
     *
     * @return string|null
     */
    private function mapAttributesToOrderState($order, $attributes)
    {
        /** @var string|null */
        $checkProcessState = $this->getAttribute($attributes, self::ATTRIBUTE_NAME_CHECK_PROCESS_STATE);
        $hasCancelReported = boolval($this->getAttribute($attributes, self::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED));
        $hasCreateInvoiceReported = boolval($this->getAttribute($attributes, self::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED));
        $hasRefundReported = boolval($this->getAttribute($attributes, self::ATTRIBUTE_NAME_HAS_REFUND_REPORTED));
        $paymentState = $order->getPaymentStatus();

        switch ($checkProcessState) {
            case OrderCheckProcessStates::CHECKED:
            case OrderCheckProcessStates::FAILED:
                return OrderStates::CHECKOUT_FAILED;
            case OrderCheckProcessStates::CONFIRMED:
                if (Status::PAYMENT_STATE_COMPLETELY_PAID === $paymentState->getId()) {
                    return OrderStates::COMPLETELY_PAID;
                }
                if ($hasRefundReported) {
                    return OrderStates::COMPLETELY_REFUNDED;
                }
                if ($hasCreateInvoiceReported) {
                    return OrderStates::INVOICED;
                }
                if ($hasCancelReported) {
                    return OrderStates::CANCELED;
                }

                return OrderStates::CHECKOUT_CONFIRMED;

            case OrderCheckProcessStates::UNCHECKED:
            default:
                return null;
        }
    }

    /**
     * @param array<string,mixed> $attributes
     * @param string              $key
     *
     * @return mixed|null
     */
    private function getAttribute($attributes, $key)
    {
        return isset($attributes[$key]) ? $attributes[$key] : null;
    }

    /**
     * @return void
     */
    private function deleteOldColumns()
    {
        $this->crudService->delete(self::ORDER_ATTRIBUTES_TABLE_NAME, self::ATTRIBUTE_NAME_CHECK_PROCESS_STATE);
        $this->crudService->delete(self::ORDER_ATTRIBUTES_TABLE_NAME, self::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED);
        $this->crudService->delete(self::ORDER_ATTRIBUTES_TABLE_NAME, self::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED);
        $this->crudService->delete(self::ORDER_ATTRIBUTES_TABLE_NAME, self::ATTRIBUTE_NAME_HAS_REFUND_REPORTED);
    }
}
