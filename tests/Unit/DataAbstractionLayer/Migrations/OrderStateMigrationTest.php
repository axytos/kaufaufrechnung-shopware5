<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\DataAbstractionLayer\Migrations;

use Axytos\ECommerce\Order\OrderCheckProcessStates;
use Axytos\KaufAufRechnung\Core\Model\OrderStateMachine\OrderStates;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations\MigrationsRepository;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations\OrderStateMigration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\DataLoader;
use Shopware\Bundle\AttributeBundle\Service\DataPersister;
use Shopware\Bundle\AttributeBundle\Service\TableMapping;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Repository;
use Shopware\Models\Order\Status;

/**
 * @internal
 */
class OrderStateMigrationTest extends TestCase
{
    /**
     * @var TableMapping&MockObject
     */
    private $tableMapping;

    /**
     * @var DataLoader&MockObject
     */
    private $dataLoader;

    /**
     * @var DataPersister&MockObject
     */
    private $dataPersister;

    /**
     * @var CrudService&MockObject
     */
    private $crudService;

    /**
     * @var ModelManager&MockObject
     */
    private $modelManager;

    /**
     * @var MigrationsRepository&MockObject
     */
    private $migrationsRepository;

    /**
     * @var OrderStateMigration
     */
    private $sut;

    /**
     * @before
     *
     * @return void
     */
    #[Before]
    public function beforeEach()
    {
        $this->tableMapping = $this->createMock(TableMapping::class);
        $this->dataLoader = $this->createMock(DataLoader::class);
        $this->dataPersister = $this->createMock(DataPersister::class);
        $this->crudService = $this->createMock(CrudService::class);
        $this->modelManager = $this->createMock(ModelManager::class);
        $this->migrationsRepository = $this->createMock(MigrationsRepository::class);

        $this->sut = new OrderStateMigration(
            $this->tableMapping,
            $this->dataLoader,
            $this->dataPersister,
            $this->crudService,
            $this->modelManager,
            $this->migrationsRepository
        );
    }

    /**
     * @return void
     */
    public function test_is_migration_needed_returns_true_if_check_process_state_column_exists()
    {
        $this->tableMapping
            ->method('isTableColumn')
            ->with(OrderStateMigration::ORDER_ATTRIBUTES_TABLE_NAME, OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE)
            ->willReturn(true)
        ;

        $result = $this->sut->isMigrationNeeded();
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function test_is_migration_needed_returns_false_if_check_process_state_column_does_not_exists()
    {
        $this->tableMapping
            ->method('isTableColumn')
            ->with(OrderStateMigration::ORDER_ATTRIBUTES_TABLE_NAME, OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE)
            ->willReturn(false)
        ;

        $result = $this->sut->isMigrationNeeded();
        $this->assertFalse($result);
    }

    /**
     * @dataProvider columnMigrationData
     *
     * @param array<string,mixed> $attributes
     * @param int                 $paymentState
     * @param string              $expectedOrderStatus
     *
     * @phpstan-param \Shopware\Models\Order\Status::* $paymentState
     * @phpstan-param \Axytos\KaufAufRechnung\Core\Model\OrderStateMachine\OrderStates::* $expectedOrderStatus
     *
     * @return void
     */
    #[DataProvider('columnMigrationData')]
    public function test_migrate_create_order_state_from_old_column_data($attributes, $paymentState, $expectedOrderStatus)
    {
        $orderId = 'test-order-id';

        /** @var Status&MockObject */
        $paymentStatus = $this->createMock(Status::class);
        $paymentStatus
            ->method('getId')
            ->willReturn($paymentState)
        ;

        /** @var Order&MockObject */
        $order = $this->createMock(Order::class);
        $order
            ->method('getPaymentStatus')
            ->willReturn($paymentStatus)
        ;

        /** @var Repository&MockObject */
        $orderRepository = $this->createMock(Repository::class);
        $orderRepository
            ->method('find')
            ->with($orderId)
            ->willReturn($order)
        ;

        $this->migrationsRepository
            ->method('getOrderIdsWhereOrderStateMigrationIsNeeded')
            ->willReturn([$orderId])
        ;
        $this->modelManager
            ->method('getRepository')
            ->with(Order::class)
            ->willReturn($orderRepository)
        ;
        $this->dataLoader
            ->method('load')
            ->with(OrderStateMigration::ORDER_ATTRIBUTES_TABLE_NAME, $orderId)
            ->willReturn($attributes)
        ;

        $resultAttributes = $attributes;
        $resultAttributes[OrderAttributesRepository::ATTRIBUTE_NAME_ORDER_STATE] = $expectedOrderStatus;
        $this->dataPersister
            ->expects($this->once())
            ->method('persist')
            ->with($resultAttributes, OrderStateMigration::ORDER_ATTRIBUTES_TABLE_NAME, $orderId)
        ;

        $this->sut->migrate();
    }

    /**
     * @return mixed[]
     */
    public static function columnMigrationData()
    {
        return [
            'undefined' => [
                [],
                34234325, // invalid payment state
                null,
            ],
            'unchecked' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::UNCHECKED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => false,
                ],
                Status::PAYMENT_STATE_OPEN,
                null,
            ],
            'unchecked (invalid)' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::UNCHECKED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => true,
                ],
                Status::PAYMENT_STATE_COMPLETELY_PAID,
                null,
            ],
            'failed' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::FAILED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => false,
                ],
                Status::PAYMENT_STATE_OPEN,
                OrderStates::CHECKOUT_FAILED,
            ],
            'failed (invalid)' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::FAILED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => true,
                ],
                Status::PAYMENT_STATE_COMPLETELY_PAID,
                OrderStates::CHECKOUT_FAILED,
            ],
            'checked' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CHECKED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => false,
                ],
                Status::PAYMENT_STATE_OPEN,
                OrderStates::CHECKOUT_FAILED,
            ],
            'checked (invalid)' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CHECKED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => true,
                ],
                Status::PAYMENT_STATE_COMPLETELY_PAID,
                OrderStates::CHECKOUT_FAILED,
            ],
            'confirmed' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CONFIRMED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => false,
                ],
                Status::PAYMENT_STATE_OPEN,
                OrderStates::CHECKOUT_CONFIRMED,
            ],
            'canceled' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CONFIRMED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => false,
                ],
                Status::PAYMENT_STATE_OPEN,
                OrderStates::CANCELED,
            ],
            'invoiced' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CONFIRMED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => false,
                ],
                Status::PAYMENT_STATE_OPEN,
                OrderStates::INVOICED,
            ],
            'refunded' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CONFIRMED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => true,
                ],
                Status::PAYMENT_STATE_OPEN,
                OrderStates::COMPLETELY_REFUNDED,
            ],
            'paid' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CONFIRMED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => false,
                ],
                Status::PAYMENT_STATE_COMPLETELY_PAID,
                OrderStates::COMPLETELY_PAID,
            ],
            'canceled and invoiced' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CONFIRMED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => false,
                ],
                Status::PAYMENT_STATE_OPEN,
                OrderStates::INVOICED,
            ],
            'canceled and refunded' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CONFIRMED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => true,
                ],
                Status::PAYMENT_STATE_OPEN,
                OrderStates::COMPLETELY_REFUNDED,
            ],
            'invoiced and refunded' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CONFIRMED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => false,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => true,
                ],
                Status::PAYMENT_STATE_OPEN,
                OrderStates::COMPLETELY_REFUNDED,
            ],
            'canceled, invoiced and refunded' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CONFIRMED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => true,
                ],
                Status::PAYMENT_STATE_OPEN,
                OrderStates::COMPLETELY_REFUNDED,
            ],
            'canceled, invoiced, refunded and paid' => [
                [
                    OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => OrderCheckProcessStates::CONFIRMED,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED => true,
                    OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED => true,
                ],
                Status::PAYMENT_STATE_COMPLETELY_PAID,
                OrderStates::COMPLETELY_PAID,
            ],
        ];
    }

    /**
     * @return void
     */
    public function test_migrate_deletes_old_columns()
    {
        $this->migrationsRepository
            ->method('getOrderIdsWhereOrderStateMigrationIsNeeded')
            ->willReturn([])
        ;

        $deletions = [];
        $this->crudService
            ->expects($this->exactly(4))
            ->method('delete')
            ->willReturnCallback(function ($table, $column) use (&$deletions) {
                $deletions[] = [$table, $column];
            })
        ;

        $this->sut->migrate();

        $this->assertEquals([
            [OrderStateMigration::ORDER_ATTRIBUTES_TABLE_NAME, OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE],
            [OrderStateMigration::ORDER_ATTRIBUTES_TABLE_NAME, OrderStateMigration::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED],
            [OrderStateMigration::ORDER_ATTRIBUTES_TABLE_NAME, OrderStateMigration::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED],
            [OrderStateMigration::ORDER_ATTRIBUTES_TABLE_NAME, OrderStateMigration::ATTRIBUTE_NAME_HAS_REFUND_REPORTED],
        ], $deletions);
    }
}
