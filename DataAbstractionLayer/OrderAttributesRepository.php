<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer;

use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\DataLoader;
use Shopware\Bundle\AttributeBundle\Service\DataPersister;
use Shopware\Bundle\AttributeBundle\Service\TableMapping;
use Shopware\Components\Model\ModelManager;

/**
 * Typedefs for Attribute Definitions
 *
 * @phpstan-type AttributeUnifiedType 'string'|'text'|'html'|'integer'|'float'|'boolean'|'date'|'datetime'|'combobox'|'single_selection'|'multi_selection'
 * @phpstan-type AttributeDefaultValueType string|int|float|null
 * @phpstan-type AttributeDefinition array{'name':string,'type':AttributeUnifiedType,'default':AttributeDefaultValueType}
 *
 * Default value MUST be a supported database type, i.e. int instead of bool
 *
 * see: https://developers.shopware.com/developers-guide/attribute-system/
 */
class OrderAttributesRepository
{
    /**
     * @return OrderAttributesRepository
     */
    public static function create()
    {
        /** @var \Shopware\Bundle\AttributeBundle\Service\CrudService */
        $crudService = Shopware()->Container()->get('shopware_attribute.crud_service');

        /** @var \Shopware\Bundle\AttributeBundle\Service\DataLoader */
        $dataLoader = Shopware()->Container()->get('shopware_attribute.data_loader');

        /** @var \Shopware\Bundle\AttributeBundle\Service\DataPersister */
        $dataPersister = Shopware()->Container()->get('shopware_attribute.data_persister');

        /** @var \Shopware\Bundle\AttributeBundle\Service\TableMapping */
        $tableMapping = Shopware()->Container()->get('shopware_attribute.table_mapping');

        /** @var \Shopware\Components\Model\ModelManager */
        $modelManager = Shopware()->Container()->get('models');

        $legacyOrderAttributesRepository = new LegacyOrderAttributesRepository($crudService, $dataLoader, $dataPersister);

        return new OrderAttributesRepository(
            $crudService,
            $dataLoader,
            $dataPersister,
            $tableMapping,
            $modelManager,
            $legacyOrderAttributesRepository
        );
    }

    const ATTRIBUTE_NAME_CHECK_PROCESS_STATE = 'axytos_kauf_auf_rechnung_check_process_state';
    const ATTRIBUTE_NAME_HAS_CANCEL_REPORTED = 'axytos_kauf_auf_rechnung_has_cancel_reported';
    const ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED = 'axytos_kauf_auf_rechnung_has_create_invoice_reported';
    const ATTRIBUTE_NAME_HAS_REFUND_REPORTED = 'axytos_kauf_auf_rechnung_has_refund_reported';
    const ATTRIBUTE_NAME_HAS_SHIPPING_REPORTED = 'axytos_kauf_auf_rechnung_has_shipping_reported';
    const ATTRIBUTE_NAME_PRECHECK_RESPONSE = 'axytos_kauf_auf_rechnung_precheck_response';
    const ATTRIBUTE_NAME_REPORTED_TRACKING_CODE = 'axytos_kauf_auf_rechnung_reported_tracking_code';

    /**
     * @var array<AttributeDefinition>
     */
    private static $attributeDefinitions = [
        [
            'name' => self::ATTRIBUTE_NAME_CHECK_PROCESS_STATE,
            'type' => 'string',
            'default' => null
        ],
        [
            'name' => self::ATTRIBUTE_NAME_HAS_CANCEL_REPORTED,
            'type' => 'boolean',
            'default' => 0
        ],
        [
            'name' => self::ATTRIBUTE_NAME_HAS_CREATE_INVOICE_REPORTED,
            'type' => 'boolean',
            'default' => 0
        ],
        [
            'name' => self::ATTRIBUTE_NAME_HAS_REFUND_REPORTED,
            'type' => 'boolean',
            'default' => 0
        ],
        [
            'name' => self::ATTRIBUTE_NAME_HAS_SHIPPING_REPORTED,
            'type' => 'boolean',
            'default' => 0
        ],
        [
            'name' => self::ATTRIBUTE_NAME_PRECHECK_RESPONSE,
            'type' => 'string',
            'default' => null
        ],
        [
            'name' => self::ATTRIBUTE_NAME_REPORTED_TRACKING_CODE,
            'type' => 'string',
            'default' => null
        ],
    ];

    /**
     * @var \Shopware\Bundle\AttributeBundle\Service\CrudService
     */
    private $crudService;
    /**
     * @var \Shopware\Bundle\AttributeBundle\Service\DataLoader
     */
    private $dataLoader;
    /**
     * @var \Shopware\Bundle\AttributeBundle\Service\DataPersister
     */
    private $dataPersister;

    /**
     * @var \Shopware\Bundle\AttributeBundle\Service\TableMapping
     */
    private $tableMapping;

    /**
     * @var \Shopware\Components\Model\ModelManager
     */
    private $modelManager;

    /**
     * @var LegacyOrderAttributesRepository
     */
    private $legacyOrderAttributesRepository;

    public function __construct(
        CrudService $crudService,
        DataLoader $dataLoader,
        DataPersister $dataPersister,
        TableMapping $tableMapping,
        ModelManager $modelManager,
        LegacyOrderAttributesRepository $legacyOrderAttributesRepository
    ) {
        $this->crudService = $crudService;
        $this->dataLoader = $dataLoader;
        $this->dataPersister = $dataPersister;
        $this->tableMapping = $tableMapping;
        $this->modelManager = $modelManager;
        $this->legacyOrderAttributesRepository = $legacyOrderAttributesRepository;
    }

    /**
     * @return void
     */
    public function install()
    {
        foreach (self::$attributeDefinitions as $attributeDefinition) {
            /** @var string */
            $name = $attributeDefinition['name'];
            /** @var string */
            $type = $attributeDefinition['type'];
            /** @var string|int|float|null */
            $defaultValue = $this->parseDefaultValue($type, $attributeDefinition['default']);

            $this->crudService->update('s_order_attributes', $name, $type, [], null, false, $defaultValue);
        }

        $this->modelManager->generateAttributeModels();

        $this->migrateLegacyAttributeValues();
    }

    /**
     * @param string $type
     * @param string|int|float|null|bool $defaultValue
     * @return string|int|float|null
     */
    private function parseDefaultValue($type, $defaultValue)
    {
        if (strtolower($type) === 'boolean' || is_bool($defaultValue)) {
            return ((bool)$defaultValue) === true ? 1 : 0;
        }
        return $defaultValue;
    }

    /**
     * @return void
     */
    public function update()
    {
        $this->install();
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @param array $data
     * @return void
     */
    public function persistPreCheckResponseData($order, $data)
    {
        $serialized = json_encode($data);
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        $order->getAttribute()->setAxytosKaufAufRechnungPrecheckresponse($serialized);

        $this->modelManager->persist($order);
        $this->modelManager->flush();
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return array
     */
    public function loadPreCheckResponseData($order)
    {
        $result = [];

        // load from new model
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        $serializedPrecheckRepsonse = $order->getAttribute()->getAxytosKaufAufRechnungPrecheckresponse();

        /** @var array */
        $result = json_decode($serializedPrecheckRepsonse, true);

        // fallback: read old field
        if (empty($serializedPrecheckRepsonse) && $this->legacyAttributesPresent()) {
            $result = $this->legacyOrderAttributesRepository->loadPreCheckResponseData($order);
        }

        return $result;
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @param string $state
     * @return void
     */
    public function persistOrderProcessState($order, $state)
    {
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        $order->getAttribute()->setAxytosKaufAufRechnungCheckprocessstate($state);

        $this->modelManager->persist($order);
        $this->modelManager->flush();
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return string
     */
    public function loadOrderProcessState($order)
    {
        // load from new model
        /** @phpstan-ignore-next-line because this method is generated by shopware */
        $state = $order->getAttribute()->getAxytosKaufAufRechnungCheckprocessstate();

        // fallback: read old field
        if (empty($state) && $this->legacyAttributesPresent()) {
            $state = $this->legacyOrderAttributesRepository->loadOrderProcessState($order);
        }

        return $state;
    }

    /**
     * @return void
     */
    private function migrateLegacyAttributeValues()
    {
        if (!$this->legacyAttributesPresent()) {
            return;
        }

        $ordreIds = $this->legacyOrderAttributesRepository->getOrderIdsWhereLegacyAttributeValuesArePresent();

        foreach ($ordreIds as $ordreId) {
            /** @var array<string,mixed> */
            $attributes = $this->dataLoader->load('s_order_attributes', $ordreId);

            /** @var string */
            $oldValue = $attributes[LegacyOrderAttributesRepository::COLUMN_NAME];

            /** @var array */
            $oldValues = json_decode($oldValue, true);

            if (empty($attributes[self::ATTRIBUTE_NAME_CHECK_PROCESS_STATE])) {
                $attributes[self::ATTRIBUTE_NAME_CHECK_PROCESS_STATE] = $oldValues[LegacyOrderAttributesRepository::ORDER_PROCESS_STATE_NAME];
            }

            if (empty($attributes[self::ATTRIBUTE_NAME_PRECHECK_RESPONSE])) {
                $attributes[self::ATTRIBUTE_NAME_PRECHECK_RESPONSE] = json_encode($oldValues[LegacyOrderAttributesRepository::PRECHECK_DATA_NAME]);
            }

            $this->dataPersister->persist($attributes, 's_order_attributes', $ordreId);
        }
    }

    /**
     * @return bool
     */
    private function legacyAttributesPresent()
    {
        return $this->tableMapping->isTableColumn('s_order_attributes', LegacyOrderAttributesRepository::COLUMN_NAME);
    }
}
