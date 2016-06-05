<?php

namespace Zeus\Barcode\DHL;

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\FixedLengthInterface;

/**
 * Abstract class to create Deutsche Post (DHL) barcodes.
 *
 * @author Rafael M. Salvioni
 */
abstract class AbstractDHL extends Interleaved implements FixedLengthInterface
{
    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $data  = \str_split($data);
        $sum   = self::sumAlternateWeight($data, 4, 9);
        $check = (10 - ($sum % 10)) % 10;
        return $check;
    }
}
