<?php

namespace AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation;

interface HashAlgorithmInterface
{
    /**
     * @param string $input
     *
     * @return string
     */
    public function compute($input);
}
