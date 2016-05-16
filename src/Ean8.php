<?php

namespace Zeus\Barcode;

/**
 * Implements a EAN8 barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean8.phtml
 */
class Ean8 extends AbstractChecksumBarcode
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
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        return self::checkSumMod10($data);
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return \preg_match("/^[0-9]{8}$/", $data);
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
