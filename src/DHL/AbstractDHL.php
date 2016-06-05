<?php

namespace Zeus\Barcode\DHL;

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\FixedLengthInterface;
use \Zeus\Barcode\FieldsTrait;

/**
 * Abstract class to create Deutsche Post (DHL) barcodes.
 *
 * @author Rafael M. Salvioni
 */
abstract class AbstractDHL extends Interleaved implements FixedLengthInterface
{
    use FieldsTrait;
    
    /**
     * Remove data mask from $data.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        $data = \preg_replace('/\. /', '', $data);
        parent::__construct($data, $hasChecksum);
    }

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
