<?php

declare(strict_types=1);

namespace AxytosKaufAufRechnungShopware5\Core;

use Shopware\Bundle\AttributeBundle\Service\CrudServiceInterface;
use Shopware\Bundle\AttributeBundle\Service\DataLoaderInterface;
use Shopware\Bundle\AttributeBundle\Service\DataPersisterInterface;
use Shopware\Models\Order\Order;

class OrderAttributesRepository
{
    private const COLUMN_NAME = 'axytos_kauf_auf_rechnung_attributes';
    private const PRECHECK_DATA_NAME = 'PreCheckResponse';
    private const ORDER_PROCESS_STATE_NAME = 'OrderProcessState';

    private CrudServiceInterface $crudService;
    private DataLoaderInterface $dataLoader;
    private DataPersisterInterface $dataPersister;

    public function __construct(CrudServiceInterface $crudService, DataLoaderInterface $dataLoader, DataPersisterInterface $dataPersister)
    {
        $this->crudService = $crudService;
        $this->dataLoader = $dataLoader;
        $this->dataPersister = $dataPersister;
    }

    public function install(): void
    {
        $this->crudService->update('s_order_attributes', self::COLUMN_NAME, 'string');
    }

    public function persistPreCheckResponseData(Order $order, array $data): void
    {
        $this->persistAttributes($order, [
            self::PRECHECK_DATA_NAME => $data
        ]);
    }

    public function loadPreCheckResponseData(Order $order): array
    {
        $orderAttributes = $this->loadAttributes($order);
        return $orderAttributes[self::PRECHECK_DATA_NAME];
    }

    public function persistOrderProcessState(Order $order, string $data): void
    {
        $this->persistAttributes($order, [
            self::ORDER_PROCESS_STATE_NAME => $data
        ]);
    }

    public function loadOrderProcessState(Order $order): string
    {
        $orderAttributes = $this->loadAttributes($order);

        /** @var string */
        return $orderAttributes[self::ORDER_PROCESS_STATE_NAME];
    }


    private function loadAttributes(Order $order): array
    {
        $allAttributes = $this->dataLoader->load("s_order_attributes", $order->getId());
        $orderAttributesData = $allAttributes[self::COLUMN_NAME];

        if (!$orderAttributesData) {
            return [];
        }

        /** @var array */
        return json_decode($orderAttributesData, true);
    }

    private function persistAttributes(Order $order, array $orderAttributes): void
    {
        $oldData = $this->loadAttributes($order);
        $newData = array_merge($oldData, $orderAttributes);
        $oderAttributesData = [self::COLUMN_NAME => json_encode($newData)];
        $this->dataPersister->persist($oderAttributesData, "s_order_attributes", $order->getId());
    }
}
