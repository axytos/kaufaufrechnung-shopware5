<?php

namespace AxytosKaufAufRechnungShopware5\Tests\Unit\Adapter\Common\HashCalculation;

use AxytosKaufAufRechnungShopware5\Adapter\Common\HashCalculation\SHA256HashAlgorithm;
use PHPUnit\Framework\TestCase;

class SHA256HashAlgorithmTest extends TestCase
{
    /**
     * @return void
     */
    public function test()
    {
        $sut = new SHA256HashAlgorithm();

        $input = 'hash me if you can';

        $expected = hash('sha256', $input);
        $actual = $sut->compute($input);

        $this->assertEquals($expected, $actual);
    }
}
