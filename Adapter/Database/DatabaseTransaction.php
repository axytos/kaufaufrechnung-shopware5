<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Database;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Database\DatabaseTransactionInterface;
use Shopware\Components\Model\ModelManager;

class DatabaseTransaction implements DatabaseTransactionInterface
{
    /**
     * @return void
     */
    public function begin()
    {
        /** @var ModelManager */
        $modelManager = Shopware()->Container()->get('models');
        $modelManager->beginTransaction();
    }

    /**
     * @return void
     */
    public function commit()
    {
        /** @var ModelManager */
        $modelManager = Shopware()->Container()->get('models');
        $modelManager->commit();
    }

    /**
     * @return void
     */
    public function rollback()
    {
        /** @var ModelManager */
        $modelManager = Shopware()->Container()->get('models');
        $modelManager->rollback();
    }
}
