<?php

namespace Zeus\Barcode;

/**
 * Implements a barcode using the Industrial 2 of 5 standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/2of5.phtml
 */
class IndustrialCode25 extends AbstractBarcode
{
    /**
     * Encoding table
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => 'NNWWN', '1' => 'WNNNW', '2' => 'NWNNW', '3' => 'WWNNN',
        '4' => 'NNWNW', '5' => 'WNWNN', '6' => 'NWWNN', '7' => 'NNNWW',
        '8' => 'WNNWN', '9' => 'NWNWN',
    ];
    
    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $data = \str_split($data);
        $sum  = 0;
        foreach ($data as $i => &$num) {
            $sum += (($i % 2) == 0 ? 3 : 1) * (int)$num;
        }
        return ($sum % 10);
    }

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        return \preg_match('/^[0-9]+$/', $data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $encoded = '11011010';
        $data    = \str_split($data);
        
        foreach ($data as &$char) {
            $encodedChar =& self::$encodingTable[$char];
            for ($i = 0; $i < 5; $i++) {
                $encoded .= ($encodedChar{$i} == 'W' ? '111' : '1') . '0';
            }
        }
        
        $encoded .= '1101011';
        return $encoded;
    }
}
