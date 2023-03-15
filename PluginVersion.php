<?php

namespace AxytosKaufAufRechnungShopware5;

class PluginVersion
{
    /**
     * @return string
     */
    public static function getVersion()
    {
        /** @phpstan-ignore-next-line */
        return json_decode(file_get_contents(__DIR__ . '/composer.json'), true)['version'];
    }
}
