<?php

namespace Zeus\Barcode\Upc;

use Zeus\Barcode\AbstractChecksumBarcode;
use Zeus\Barcode\FixedLengthInterface;

/**
 * Implements a EAN-8 barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean8.phtml
 */
class Ean8 extends AbstractChecksumBarcode implements FixedLengthInterface
{
    use EanHelperTrait;
    
    /**
     * Padding zeros left on $data to complete the necessary length.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        $data = self::zeroLeftPadding($data, $hasChecksum ? 8 : 7);
        parent::__construct($data, $hasChecksum);
    }
    
    /**
     * Returns 8.
     * 
     * @return int
     */
    public function getLength()
    {
        return 8;
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $encoded   = '';
        
        for ($i = 0; $i < 8; $i++) {
            $parity   = $i < 4 ? 0 : 2;
            $encoded .= self::$encodingTable[$data{$i}][$parity];
        }
        
        $encoded  = '101' .
                    \substr($encoded, 0, 28) .
                    '01010' .
                    \substr($encoded, 28) .
                    '101';
        
        return $encoded;
    }
}
