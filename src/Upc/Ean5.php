<?php

namespace Zeus\Barcode\Upc;

use Zeus\Barcode\AbstractBarcode;
use Zeus\Barcode\FixedLengthInterface;

/**
 * Implements a EAN-5 supplemental barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/upcext.phtml
 */
class Ean5 extends AbstractBarcode implements FixedLengthInterface
{
    use EanHelperTrait;
    
    /**
     * Parity table
     * 
     * 0 => Odd
     * 1 => Even
     * 
     * @var array
     */
    protected static $parityTable = [
        '0' => [1, 1, 0, 0, 0], '1' => [1, 0, 1, 0, 0],
        '2' => [1, 0, 0, 1, 0], '3' => [1, 0, 0, 0, 1],
        '4' => [0, 1, 1, 0, 0], '5' => [0, 0, 1, 1, 0],
        '6' => [0, 0, 0, 1, 1], '7' => [0, 1, 0, 1, 0],
        '8' => [0, 1, 0, 0, 1], '9' => [0, 0, 1, 0, 1],
    ];
    
    /**
     * Returns 5.
     * 
     * @return int
     */
    public function getLength()
    {
        return 5;
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $data      = \str_split($data);
        $check     = \substr(self::sumAlternateWeight($data, 3, 9), -1);
        $parityTab =& self::$parityTable[$check];
        $encoded   = '1011';
                    
        foreach ($data as $i => &$char) {
            $encoded .= self::$encodingTable[$char][$parityTab[$i]];
            $encoded .= '01';
        }
        
        $encoded   = \substr($encoded, 0, -2);
        return $encoded;
    }
}
