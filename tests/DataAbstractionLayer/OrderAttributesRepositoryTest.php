<?php

namespace AxytosKaufAufRechnungShopware5\Tests\DataAbstractionLayer;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\LegacyOrderAttributesRepository;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\DataLoader;
use Shopware\Bundle\AttributeBundle\Service\DataPersister;
use Shopware\Bundle\AttributeBundle\Service\TableMapping;
use Shopware\Components\Model\ModelManager;

class OrderAttributesRepositoryTest extends TestCase
{
    /**
     * @var \Shopware\Bundle\AttributeBundle\Service\CrudService&MockObject
     */
    private $crudService;
    /**
     * @var \Shopware\Bundle\AttributeBundle\Service\DataLoader&MockObject
     */
    private $dataLoader;
    /**
     * @var \Shopware\Bundle\AttributeBundle\Service\DataPersister&MockObject
     */
    private $dataPersister;

    /**
     * @var \Shopware\Bundle\AttributeBundle\Service\TableMapping&MockObject
     */
    private $tableMapping;

    /**
     * @var \Shopware\Components\Model\ModelManager&MockObject
     */
    private $modelManager;

    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\LegacyOrderAttributesRepository&MockObject
     */
    private $legacyOrderAttributesRepository;

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
        $this->dataLoader = $this->createMock(DataLoader::class);
        $this->dataPersister = $this->createMock(DataPersister::class);
        $this->tableMapping = $this->createMock(TableMapping::class);
        $this->modelManager = $this->createMock(ModelManager::class);
        $this->legacyOrderAttributesRepository = $this->createMock(LegacyOrderAttributesRepository::class);

        $this->sut = new OrderAttributesRepository(
            $this->crudService,
            $this->dataLoader,
            $this->dataPersister,
            $this->tableMapping,
            $this->modelManager,
            $this->legacyOrderAttributesRepository
        );
    }

    /**
     * @return void
     */
    public function test_install_does_not_delete_legacy_attribute_column()
    {
        $this->crudService->expects($this->never())->method('delete')->with('s_order_attributes', 'axytos_kauf_auf_rechnung_attributes');

        $this->sut->install();
    }


    /**
     * @return void
     */
    public function test_install_does_not_migrate_legacy_attribute_values_if_legacy_attribute_column_is_not_present()
    {
        $this->tableMapping->method('isTableColumn')->with('s_order_attributes', 'axytos_kauf_auf_rechnung_attributes')->willReturn(false);

        $this->legacyOrderAttributesRepository->expects($this->never())->method('getOrderIdsWhereLegacyAttributeValuesArePresent');
        $this->dataLoader->expects($this->never())->method('load');
        $this->dataPersister->expects($this->never())->method('persist');

        $this->sut->install();
    }

    /**
     * @return void
     */
    public function test_install_migrates_all_legacy_attribute_values_if_legacy_attribute_column_is_present()
    {
        $oldAttributeValues = [];
        $expectedNewAttributeValues = [];
        $actualNewAttributeValues = [];
        $actual = [];

        for ($id = 0; $id < 5; $id++) {
            $orderProcessState = "OrderProcessState-$id";
            $preCheckResponse = [
                'SomeKey' => "PreCheckResponse-$id"
            ];
            $oldAttributeValues[$id] = [
                'axytos_kauf_auf_rechnung_attributes' => json_encode([
                    'OrderProcessState' => $orderProcessState,
                    'PreCheckResponse' => $preCheckResponse
                ])
            ];
            $expectedNewAttributeValues[$id] = [
                'axytos_kauf_auf_rechnung_attributes' => $oldAttributeValues[$id]['axytos_kauf_auf_rechnung_attributes'],
                'axytos_kauf_auf_rechnung_check_process_state' => $orderProcessState,
                'axytos_kauf_auf_rechnung_precheck_response' => json_encode($preCheckResponse)
            ];
        }

        $this->tableMapping->method('isTableColumn')->with('s_order_attributes', 'axytos_kauf_auf_rechnung_attributes')->willReturn(true);

        $this->legacyOrderAttributesRepository->method('getOrderIdsWhereLegacyAttributeValuesArePresent')->willReturn(array_keys($oldAttributeValues));

        $this->dataLoader->method('load')->willReturnCallback(function ($table, $orderId) use (&$oldAttributeValues) {
            if ($table === 's_order_attributes') {
                return $oldAttributeValues[$orderId];
            }
            return null;
        });

        $this->dataPersister->method('persist')->willReturnCallback(function ($attributes, $table, $orderId) use (&$actualNewAttributeValues) {
            if ($table === 's_order_attributes') {
                $actualNewAttributeValues[$orderId] = $attributes;
            }
        });

        $this->sut->install();

        foreach ($actualNewAttributeValues as $id => $actualNewAttributeValue) {
            $this->assertEquals($expectedNewAttributeValues[$id], $actualNewAttributeValue);
        }
    }
}
