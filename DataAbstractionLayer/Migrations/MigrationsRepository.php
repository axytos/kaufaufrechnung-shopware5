<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations;

use AxytosKaufAufRechnungShopware5\DataAbstractionLayer\OrderAttributesRepository;

class MigrationsRepository
{
    /**
     * @return string[]
     */
    public function getOrderIdsWhereLegacyAttributeValuesArePresent()
    {
        $stmt = Shopware()->Db()->query('SELECT orderID FROM s_order_attributes WHERE ' . LegacyOrderAttributesMigration::LEGACY_COLUMN_NAME . ' IS NOT NULL;');
        return $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
    }

    /**
     * @return string[]
     */
    public function getOrderIdsWhereOrderStateMigrationIsNeeded()
    {
        $stmt = Shopware()->Db()->query(
            'SELECT orderID FROM s_order_attributes WHERE '
                . OrderStateMigration::ATTRIBUTE_NAME_CHECK_PROCESS_STATE
                . ' IS NOT NULL AND '
                . OrderAttributesRepository::ATTRIBUTE_NAME_ORDER_STATE
                . ' IS NULL;'
        );
        return $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
    }
}
