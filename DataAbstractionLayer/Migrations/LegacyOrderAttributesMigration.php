<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\DataLoader;
use Shopware\Bundle\AttributeBundle\Service\DataPersister;
use Shopware\Bundle\AttributeBundle\Service\TableMapping;

class LegacyOrderAttributesMigration implements MigrationInterface
{
    const LEGACY_COLUMN_NAME = 'axytos_kauf_auf_rechnung_attributes';
    const PRECHECK_DATA_NAME = 'PreCheckResponse';
    const ORDER_PROCESS_STATE_NAME = 'OrderProcessState';

    const ATTRIBUTE_NAME_CHECK_PROCESS_STATE = 'axytos_kauf_auf_rechnung_check_process_state';

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
     * @var MigrationsRepository
     */
    private $migrationsRepository;

    public function __construct(
        TableMapping $tableMapping,
        DataLoader $dataLoader,
        DataPersister $dataPersister,
        CrudService $crudService,
        MigrationsRepository $migrationsRepository
    ) {
        $this->tableMapping = $tableMapping;
        $this->dataLoader = $dataLoader;
        $this->dataPersister = $dataPersister;
        $this->crudService = $crudService;
        $this->migrationsRepository = $migrationsRepository;
    }

    /**
     * @return bool
     */
    public function isMigrationNeeded()
    {
        return $this->tableMapping->isTableColumn(self::ORDER_ATTRIBUTES_TABLE_NAME, self::LEGACY_COLUMN_NAME);
    }

    /**
     * @return void
     */
    public function migrate()
    {
        $this->migrateColumnData();
        $this->deleteOldColumn();
    }

    /**
     * @return void
     */
    private function migrateColumnData()
    {
        $orderIds = $this->migrationsRepository->getOrderIdsWhereLegacyAttributeValuesArePresent();
        foreach ($orderIds as $orderId) {
            /** @var array<string,mixed>
             * @phpstan-ignore-next-line */
            $attributes = $this->dataLoader->load(self::ORDER_ATTRIBUTES_TABLE_NAME, $orderId);

            if (!isset($attributes[self::LEGACY_COLUMN_NAME])) {
                continue;
            }

            /** @var string */
            $oldValue = $attributes[self::LEGACY_COLUMN_NAME];

            /** @var array<string,mixed> */
            $oldValues = json_decode($oldValue, true);

            if ($this->notSet($attributes, self::ATTRIBUTE_NAME_CHECK_PROCESS_STATE)) {
                $attributes[self::ATTRIBUTE_NAME_CHECK_PROCESS_STATE] = $oldValues[self::ORDER_PROCESS_STATE_NAME];
            }

            if ($this->notSet($attributes, OrderAttributesRepository::ATTRIBUTE_NAME_PRECHECK_RESPONSE)) {
                $attributes[OrderAttributesRepository::ATTRIBUTE_NAME_PRECHECK_RESPONSE] = json_encode($oldValues[self::PRECHECK_DATA_NAME]);
            }

            $this->dataPersister->persist($attributes, self::ORDER_ATTRIBUTES_TABLE_NAME, $orderId);
        }
    }

    /**
     * @return void
     */
    private function deleteOldColumn()
    {
        $this->crudService->delete(self::ORDER_ATTRIBUTES_TABLE_NAME, self::LEGACY_COLUMN_NAME);
    }

    /**
     * @param array<string,mixed> $attributes
     * @param string              $key
     *
     * @return bool
     */
    private function notSet($attributes, $key)
    {
        return !isset($attributes[$key]) || '' === strval($attributes[$key]);
    }
}
