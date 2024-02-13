<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Database;

use Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Database\DatabaseTransactionFactoryInterface;

class DatabaseTransactionFactory implements DatabaseTransactionFactoryInterface
{
    /**
     * @return \Axytos\KaufAufRechnung\Core\Plugin\Abstractions\Database\DatabaseTransactionInterface
     */
    public function create()
    {
        return new DatabaseTransaction();
    }
}
