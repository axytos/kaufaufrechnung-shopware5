<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations;

interface MigrationInterface
{
    /**
     * @return boolean
     */
    public function isMigrationNeeded();

    /**
     * @return void
     */
    public function migrate();
}
