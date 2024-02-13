<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\DataAbstractionLayer;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations\LegacyOrderAttributesMigration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations\OrderStateMigration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use PHPUnit\Framework\TestCase;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Components\Model\ModelManager;

class OrderAttributesRepositoryTest extends TestCase
{
    /**
     * @var \Shopware\Bundle\AttributeBundle\Service\CrudService&MockObject
     */
    private $crudService;

    /**
     * @var \Shopware\Components\Model\ModelManager&MockObject
     */
    private $modelManager;

    /**
     * @var LegacyOrderAttributesMigration&MockObject
     */
    private $legacyOrderAttributesMigration;

    /**
     * @var OrderStateMigration&MockObject
     */
    private $orderStatesMigration;

    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository
     */
    private $sut;

    /**
     * @before
     * @return void
     */
    public function beforeEach()
    {
        $this->crudService = $this->createMock(CrudService::class);
        $this->modelManager = $this->createMock(ModelManager::class);
        $this->legacyOrderAttributesMigration = $this->createMock(LegacyOrderAttributesMigration::class);
        $this->orderStatesMigration = $this->createMock(OrderStateMigration::class);

        $this->sut = new OrderAttributesRepository(
            $this->crudService,
            $this->modelManager,
            $this->legacyOrderAttributesMigration,
            $this->orderStatesMigration
        );
    }

    /**
     * @return void
     */
    public function test_install_createAttributeColumns()
    {
        $createdColumns = [];
        $this->crudService
            ->expects($this->exactly(6))
            ->method('update')
            ->willReturnCallback(function (...$args) use (&$createdColumns) {
                $createdColumns[] = $args;
            });

        $this->sut->install();

        $expectedUpdates = [
            [OrderAttributesRepository::ATTRIBUTE_NAME_ORDER_STATE, 'string', null],
            [OrderAttributesRepository::ATTRIBUTE_NAME_ORDER_STATE_DATA, 'string', null],
            [OrderAttributesRepository::ATTRIBUTE_NAME_PRECHECK_RESPONSE, 'string', null],
            [OrderAttributesRepository::ATTRIBUTE_NAME_ORDER_BASKET_HASH, 'string', null],
            [OrderAttributesRepository::ATTRIBUTE_NAME_HAS_SHIPPING_REPORTED, 'boolean', 0],
            [OrderAttributesRepository::ATTRIBUTE_NAME_REPORTED_TRACKING_CODE, 'string', null],
        ];

        $this->assertEquals(count($expectedUpdates), count($createdColumns));

        for ($i = 0; $i < count($expectedUpdates); $i++) {
            $this->assertEquals('s_order_attributes', $createdColumns[0][0]);
            $this->assertEquals($expectedUpdates[0][0], $createdColumns[0][1]);
            $this->assertEquals($expectedUpdates[0][1], $createdColumns[0][2]);
            $this->assertEmpty($createdColumns[0][3]);
            $this->assertNull($createdColumns[0][4]);
            $this->assertFalse($createdColumns[0][5]);
            $this->assertEquals($expectedUpdates[0][2], $createdColumns[0][6]);
        }
    }

    /**
     * @dataProvider migrationVariants
     * @param bool $legacyMigrationNeeded
     * @param bool $orderStateMigrationNeeded
     * @param InvocationOrder $legacyMigrationInvocations
     * @param InvocationOrder $orderStateMigrationInvocations
     * @return void
     */
    public function test_install_executesAllRequiredMigrations($legacyMigrationNeeded, $orderStateMigrationNeeded, $legacyMigrationInvocations, $orderStateMigrationInvocations)
    {
        $this->legacyOrderAttributesMigration
            ->method('isMigrationNeeded')
            ->willReturn($legacyMigrationNeeded);
        $this->orderStatesMigration
            ->method('isMigrationNeeded')
            ->willReturn($orderStateMigrationNeeded);

        $this->legacyOrderAttributesMigration
            ->expects($legacyMigrationInvocations)
            ->method('migrate');
        $this->orderStatesMigration
            ->expects($orderStateMigrationInvocations)
            ->method('migrate');

        $this->sut->install();
    }

    /**
     * @return void
     */
    public function test_install_generatesAttributeModels()
    {
        $this->modelManager
            ->expects($this->once())
            ->method('generateAttributeModels');

        $this->sut->install();
    }

    /**
     * @return void
     */
    public function test_update_createAttributeColumns()
    {
        $createdColumns = [];
        $this->crudService
            ->expects($this->exactly(6))
            ->method('update')
            ->willReturnCallback(function (...$args) use (&$createdColumns) {
                $createdColumns[] = $args;
            });

        $this->sut->update();

        $expectedUpdates = [
            [OrderAttributesRepository::ATTRIBUTE_NAME_ORDER_STATE, 'string', null],
            [OrderAttributesRepository::ATTRIBUTE_NAME_ORDER_STATE_DATA, 'string', null],
            [OrderAttributesRepository::ATTRIBUTE_NAME_PRECHECK_RESPONSE, 'string', null],
            [OrderAttributesRepository::ATTRIBUTE_NAME_ORDER_BASKET_HASH, 'string', null],
            [OrderAttributesRepository::ATTRIBUTE_NAME_HAS_SHIPPING_REPORTED, 'boolean', 0],
            [OrderAttributesRepository::ATTRIBUTE_NAME_REPORTED_TRACKING_CODE, 'string', null],
        ];

        $this->assertEquals(count($expectedUpdates), count($createdColumns));

        for ($i = 0; $i < count($expectedUpdates); $i++) {
            $this->assertEquals('s_order_attributes', $createdColumns[0][0]);
            $this->assertEquals($expectedUpdates[0][0], $createdColumns[0][1]);
            $this->assertEquals($expectedUpdates[0][1], $createdColumns[0][2]);
            $this->assertEmpty($createdColumns[0][3]);
            $this->assertNull($createdColumns[0][4]);
            $this->assertFalse($createdColumns[0][5]);
            $this->assertEquals($expectedUpdates[0][2], $createdColumns[0][6]);
        }
    }

    /**
     * @dataProvider migrationVariants
     * @param bool $legacyMigrationNeeded
     * @param bool $orderStateMigrationNeeded
     * @param InvocationOrder $legacyMigrationInvocations
     * @param InvocationOrder $orderStateMigrationInvocations
     * @return void
     */
    public function test_update_executesAllRequiredMigrations($legacyMigrationNeeded, $orderStateMigrationNeeded, $legacyMigrationInvocations, $orderStateMigrationInvocations)
    {
        $this->legacyOrderAttributesMigration
            ->method('isMigrationNeeded')
            ->willReturn($legacyMigrationNeeded);
        $this->orderStatesMigration
            ->method('isMigrationNeeded')
            ->willReturn($orderStateMigrationNeeded);

        $this->legacyOrderAttributesMigration
            ->expects($legacyMigrationInvocations)
            ->method('migrate');
        $this->orderStatesMigration
            ->expects($orderStateMigrationInvocations)
            ->method('migrate');

        $this->sut->update();
    }

    /**
     * @return void
     */
    public function test_update_generatesAttributeModels()
    {
        $this->modelManager
            ->expects($this->once())
            ->method('generateAttributeModels');

        $this->sut->update();
    }

    /**
     * @return mixed[]
     */
    public function migrationVariants()
    {
        return [
            'none' => [false, false, $this->never(), $this->never()],
            'legacy' => [true, false, $this->once(), $this->never()],
            'states' => [false, true, $this->never(), $this->once()],
            'all' => [true, true, $this->once(), $this->once()],
        ];
    }
}
