<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\DataAbstractionLayer\Migrations;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations\LegacyOrderAttributesMigration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations\MigrationsRepository;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\DataLoader;
use Shopware\Bundle\AttributeBundle\Service\DataPersister;
use Shopware\Bundle\AttributeBundle\Service\TableMapping;

class LegacyOrderAttributesMigrationTest extends TestCase
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
     * @var MigrationsRepository&MockObject
     */
    private $migrationsRepository;

    /**
     * @var LegacyOrderAttributesMigration
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    #[Before]
    public function beforeEach()
    {
        $this->tableMapping = $this->createMock(TableMapping::class);
        $this->dataLoader = $this->createMock(DataLoader::class);
        $this->dataPersister = $this->createMock(DataPersister::class);
        $this->crudService = $this->createMock(CrudService::class);
        $this->migrationsRepository = $this->createMock(MigrationsRepository::class);

        $this->sut = new LegacyOrderAttributesMigration(
            $this->tableMapping,
            $this->dataLoader,
            $this->dataPersister,
            $this->crudService,
            $this->migrationsRepository
        );
    }

    /**
     * @return void
     */
    public function test_isMigrationNeeded_returnsTrueIfLegacyColumnExists()
    {
        $this->tableMapping
            ->method('isTableColumn')
            ->with(LegacyOrderAttributesMigration::ORDER_ATTRIBUTES_TABLE_NAME, LegacyOrderAttributesMigration::LEGACY_COLUMN_NAME)
            ->willReturn(true);

        $result = $this->sut->isMigrationNeeded();
        $this->assertTrue($result);
    }

    /**
     * @return void
     */
    public function test_isMigrationNeeded_returnsFalseIfLegacyColumnDoesNotExists()
    {
        $this->tableMapping
            ->method('isTableColumn')
            ->with(LegacyOrderAttributesMigration::ORDER_ATTRIBUTES_TABLE_NAME, LegacyOrderAttributesMigration::LEGACY_COLUMN_NAME)
            ->willReturn(false);

        $result = $this->sut->isMigrationNeeded();
        $this->assertFalse($result);
    }

    /**
     * @dataProvider columnMigrationData
     * @param array<string,mixed> $attributes
     * @param string $expectedCheckProcessState
     * @param string $expectedPrecheckResponse
     * @param bool $skipped
     * @return void
     */
    #[DataProvider('columnMigrationData')]
    public function test_migrate_migrates_column_data($attributes, $expectedCheckProcessState, $expectedPrecheckResponse, $skipped = false)
    {
        $orderId = 'test-order-id';

        $this->migrationsRepository
            ->method('getOrderIdsWhereLegacyAttributeValuesArePresent')
            ->willReturn([$orderId]);
        $this->dataLoader
            ->method('load')
            ->with(LegacyOrderAttributesMigration::ORDER_ATTRIBUTES_TABLE_NAME, $orderId)
            ->willReturn($attributes);

        if ($skipped) {
            $this->dataPersister
                ->expects($this->never())
                ->method('persist');
        } else {
            $resultAttributes = $attributes;
            $resultAttributes[LegacyOrderAttributesMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE] = $expectedCheckProcessState;
            $resultAttributes[OrderAttributesRepository::ATTRIBUTE_NAME_PRECHECK_RESPONSE] = $expectedPrecheckResponse;
            $this->dataPersister
                ->expects($this->once())
                ->method('persist')
                ->with($resultAttributes, LegacyOrderAttributesMigration::ORDER_ATTRIBUTES_TABLE_NAME, $orderId);
        }

        $this->sut->migrate();
    }

    /**
     * @return mixed[]
     */
    public static function columnMigrationData()
    {
        return [
            'empty' => [
                [],
                null,
                null,
                true
            ],
            'legacy data' => [
                [
                    LegacyOrderAttributesMigration::LEGACY_COLUMN_NAME => json_encode([
                        LegacyOrderAttributesMigration::ORDER_PROCESS_STATE_NAME => 'old-state',
                        LegacyOrderAttributesMigration::PRECHECK_DATA_NAME => [
                            'a' => 1,
                            'b' => 2,
                        ],
                    ]),
                ],
                'old-state',
                '{"a":1,"b":2}',
            ],
            'legacy data and empty new data' => [
                [
                    LegacyOrderAttributesMigration::LEGACY_COLUMN_NAME => json_encode([
                        LegacyOrderAttributesMigration::ORDER_PROCESS_STATE_NAME => 'old-state',
                        LegacyOrderAttributesMigration::PRECHECK_DATA_NAME => [
                            'a' => 1,
                            'b' => 2,
                        ],
                    ]),
                    LegacyOrderAttributesMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => '',
                    OrderAttributesRepository::ATTRIBUTE_NAME_PRECHECK_RESPONSE => '',
                ],
                'old-state',
                '{"a":1,"b":2}',
            ],
            'legacy data and new data' => [
                [
                    LegacyOrderAttributesMigration::LEGACY_COLUMN_NAME => json_encode([
                        LegacyOrderAttributesMigration::ORDER_PROCESS_STATE_NAME => 'old-state',
                        LegacyOrderAttributesMigration::PRECHECK_DATA_NAME => [
                            'a' => 1,
                            'b' => 2,
                        ],
                    ]),
                    LegacyOrderAttributesMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE => 'new-state',
                    OrderAttributesRepository::ATTRIBUTE_NAME_PRECHECK_RESPONSE => '{"c":42}',
                ],
                'new-state',
                '{"c":42}',
            ],
        ];
    }

    /**
     * @return void
     */
    public function test_migrate_deletesOldColumns()
    {
        $this->migrationsRepository
            ->method('getOrderIdsWhereLegacyAttributeValuesArePresent')
            ->willReturn([]);

        $this->crudService
            ->expects($this->once())
            ->method('delete')
            ->with(LegacyOrderAttributesMigration::ORDER_ATTRIBUTES_TABLE_NAME, LegacyOrderAttributesMigration::LEGACY_COLUMN_NAME);

        $this->sut->migrate();
    }
}
