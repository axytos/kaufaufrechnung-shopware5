<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations\LegacyOrderAttributesMigration;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations\MigrationsRepository;
use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations\OrderStateMigration;
use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Components\Model\ModelManager;

/**
 * Typedefs for Attribute Definitions.
 *
 * @phpstan-type AttributeUnifiedType 'string'|'text'|'html'|'integer'|'float'|'boolean'|'date'|'datetime'|'combobox'|'single_selection'|'multi_selection'
 * @phpstan-type AttributeDefaultValueType string|int|float|null
 * @phpstan-type AttributeDefinition array{'name':string,'type':AttributeUnifiedType,'default':AttributeDefaultValueType}
 *
 * Default value MUST be a supported database type, i.e. int instead of bool
 *
 * import types with
 *   (at)phpstan-import-type AttributeUnifiedType from OrderAttributesRepository
 *
 * see:
 * - https://developers.shopware.com/developers-guide/attribute-system/
 * - https://phpstan.org/writing-php-code/phpdoc-types#literals-and-constants
 * - https://phpstan.org/writing-php-code/phpdoc-types#local-type-aliases
 */
class OrderAttributesRepository
{
    /**
     * @return OrderAttributesRepository
     */
    public static function create()
    {
        /** @var CrudService */
        $crudService = Shopware()->Container()->get('shopware_attribute.crud_service');

        /** @var \Shopware\Bundle\AttributeBundle\Service\DataLoader */
        $dataLoader = Shopware()->Container()->get('shopware_attribute.data_loader');

        /** @var \Shopware\Bundle\AttributeBundle\Service\DataPersister */
        $dataPersister = Shopware()->Container()->get('shopware_attribute.data_persister');

        /** @var \Shopware\Bundle\AttributeBundle\Service\TableMapping */
        $tableMapping = Shopware()->Container()->get('shopware_attribute.table_mapping');

        /** @var ModelManager */
        $modelManager = Shopware()->Container()->get('models');

        $migrationsRepository = new MigrationsRepository();
        $legacyOrderAttributesMigration = new LegacyOrderAttributesMigration(
            $tableMapping,
            $dataLoader,
            $dataPersister,
            $crudService,
            $migrationsRepository
        );
        $orderStateMigration = new OrderStateMigration(
            $tableMapping,
            $dataLoader,
            $dataPersister,
            $crudService,
            $modelManager,
            $migrationsRepository
        );

        return new OrderAttributesRepository(
            $crudService,
            $modelManager,
            $legacyOrderAttributesMigration,
            $orderStateMigration
        );
    }

    const ATTRIBUTE_NAME_ORDER_STATE = 'axytos_kauf_auf_rechnung_order_state';
    const ATTRIBUTE_NAME_ORDER_STATE_DATA = 'axytos_kauf_auf_rechnung_order_state_data';
    const ATTRIBUTE_NAME_PRECHECK_RESPONSE = 'axytos_kauf_auf_rechnung_precheck_response';
    const ATTRIBUTE_NAME_ORDER_BASKET_HASH = 'axytos_kauf_auf_rechnung_order_basket_hash';
    const ATTRIBUTE_NAME_HAS_SHIPPING_REPORTED = 'axytos_kauf_auf_rechnung_has_shipping_reported';
    const ATTRIBUTE_NAME_REPORTED_TRACKING_CODE = 'axytos_kauf_auf_rechnung_reported_tracking_code';

    /**
     * @var array<AttributeDefinition>
     */
    private static $attributeDefinitions = [
        [
            'name' => self::ATTRIBUTE_NAME_ORDER_STATE,
            'type' => 'string',
            'default' => null,
        ],
        [
            'name' => self::ATTRIBUTE_NAME_ORDER_STATE_DATA,
            'type' => 'string',
            'default' => null,
        ],
        [
            'name' => self::ATTRIBUTE_NAME_PRECHECK_RESPONSE,
            'type' => 'string',
            'default' => null,
        ],
        [
            'name' => self::ATTRIBUTE_NAME_ORDER_BASKET_HASH,
            'type' => 'string',
            'default' => null,
        ],
        [
            'name' => self::ATTRIBUTE_NAME_HAS_SHIPPING_REPORTED,
            'type' => 'boolean',
            'default' => 0,
        ],
        [
            'name' => self::ATTRIBUTE_NAME_REPORTED_TRACKING_CODE,
            'type' => 'string',
            'default' => null,
        ],
    ];

    /**
     * @var CrudService
     */
    private $crudService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var \AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations\MigrationInterface[]
     */
    private $migrations;

    public function __construct(
        CrudService $crudService,
        ModelManager $modelManager,
        LegacyOrderAttributesMigration $legacyOrderAttributesMigration,
        OrderStateMigration $orderStateMigration
    ) {
        $this->crudService = $crudService;
        $this->modelManager = $modelManager;
        $this->migrations = [
            $legacyOrderAttributesMigration,
            $orderStateMigration,
        ];
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

        foreach ($this->migrations as $migration) {
            if ($migration->isMigrationNeeded()) {
                $migration->migrate();
            }
        }

        $this->modelManager->generateAttributeModels();
    }

    /**
     * @param string                     $type
     * @param string|int|float|bool|null $defaultValue
     *
     * @return string|int|float|null
     */
    private function parseDefaultValue($type, $defaultValue)
    {
        if ('boolean' === strtolower($type) || is_bool($defaultValue)) {
            return ((bool) $defaultValue) === true ? 1 : 0;
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
}
