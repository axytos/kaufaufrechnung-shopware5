<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer;

use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\DataLoader;
use Shopware\Bundle\AttributeBundle\Service\DataPersister;
use Shopware\Models\Order\Order;

/**
 * LegacyOrderAttributesRepository
 *
 * To be removed in future releases when no one uses the old attribute model anymore!
 */
class LegacyOrderAttributesRepository
{
    const COLUMN_NAME = 'axytos_kauf_auf_rechnung_attributes';
    const PRECHECK_DATA_NAME = 'PreCheckResponse';
    const ORDER_PROCESS_STATE_NAME = 'OrderProcessState';

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

    public function __construct(CrudService $crudService, DataLoader $dataLoader, DataPersister $dataPersister)
    {
        $this->crudService = $crudService;
        $this->dataLoader = $dataLoader;
        $this->dataPersister = $dataPersister;
    }

    /**
     * @return void
     */
    public function install()
    {
        $this->crudService->update('s_order_attributes', self::COLUMN_NAME, 'string');
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @param array $data
     * @return void
     */
    public function persistPreCheckResponseData($order, $data)
    {
        $this->persistAttributes($order, [
            self::PRECHECK_DATA_NAME => $data
        ]);
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return array
     */
    public function loadPreCheckResponseData($order)
    {
        $orderAttributes = $this->loadAttributes($order);
        return $orderAttributes[self::PRECHECK_DATA_NAME];
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @param string $data
     * @return void
     */
    public function persistOrderProcessState($order, $data)
    {
        $this->persistAttributes($order, [
            self::ORDER_PROCESS_STATE_NAME => $data
        ]);
    }

    /**
     * @param \Shopware\Models\Order\Order $order
     * @return string
     */
    public function loadOrderProcessState($order)
    {
        $orderAttributes = $this->loadAttributes($order);

        /** @var string */
        return $orderAttributes[self::ORDER_PROCESS_STATE_NAME];
    }


    /**
     * @return array
     */
    private function loadAttributes(Order $order)
    {
        $foreignKey = $order->getId();
        /** @phpstan-ignore-next-line because type of $foreignKey changes from string to int between shopware versions */
        $allAttributes = $this->dataLoader->load("s_order_attributes", $foreignKey);
        $orderAttributesData = $allAttributes[self::COLUMN_NAME];

        if (!$orderAttributesData) {
            return [];
        }

        /** @var array */
        return json_decode($orderAttributesData, true);
    }

    /**
     * @return void
     */
    private function persistAttributes(Order $order, array $orderAttributes)
    {
        $oldData = $this->loadAttributes($order);
        $newData = array_merge($oldData, $orderAttributes);
        $oderAttributesData = [self::COLUMN_NAME => json_encode($newData)];
        $this->dataPersister->persist($oderAttributesData, "s_order_attributes", $order->getId());
    }

    /**
     * @return array
     */
    public function getOrderIdsWhereLegacyAttributeValuesArePresent()
    {
        $stmt = Shopware()->Db()->query('SELECT orderID FROM s_order_attributes WHERE ? IS NOT NULL;', [
            self::COLUMN_NAME
        ]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN, 0);
    }
}
