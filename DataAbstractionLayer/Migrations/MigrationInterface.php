<?php

namespace AxytosKaufAufRechnungShopware5\DataAbstractionLayer\Migrations;

interface MigrationInterface
{
    /**
     * @return bool
     */
    public function isMigrationNeeded();

    /**
     * @return void
     */
    public function migrate();
}
