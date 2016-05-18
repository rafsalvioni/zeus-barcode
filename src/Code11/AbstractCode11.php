<?php

namespace Zeus\Barcode\Code11;

use Zeus\Barcode\AbstractChecksumBarcode;

/**
 * Abstract implementation of Code11 barcode standard.
 * 
 * This barcode is length variable, use a optional checksum and suports
 * only numeric chars and dash.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code11.phtml
 */
abstract class AbstractCode11 extends AbstractChecksumBarcode
{
    /**
     * Encoding table
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => '101011',  '1' => '1101011', '2' => '1001011', '3' => '1100101',
        '4' => '1011011', '5' => '1101101', '6' => '1001101', '7' => '1010011',
        '8' => '1101001', '9' => '110101',  '-' => '101101',
    ];
    
    /**
     * Calculates checkdigits using a max weight and modulus divisor.
     * 
     * @param array $data Splitted data
     * @param int $maxWeight
     * @param int $divisor
     * @return int
     */
    protected static function calcCheckDigit(
        $data, $maxWeight, $divisor
    ) {
        $sum    = 0;
        $weight = 1;
        
        while (!empty($data)) {
            $num = \substr_remove($data, -1);
            if ($num == '-') {
                $num = 10;
            }
            $sum += (int)$num * $weight++;
            if ($weight > $maxWeight) {
                $weight = 1;
            }
        }
        return ($sum % $divisor);
    }
    
    /**
     * Calculates the C checksum digit.
     * 
     * @param string $data
     * @return int
     */
    protected static function calcDigitC($data)
    {
        return self::calcCheckDigit($data, 6, 11);
    }

    /**
     * Calculates the K checksum digit.
     * 
     * @param string $data
     * @return int
     */
    protected static function calcDigitK($data)
    {
        return self::calcCheckDigit($data, 7, 9);
    }
    
    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return preg_match("/^[0-9\\-]{2,}/", $data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $encoded = '';
        
        while (!empty($data)) {
            $char     = \substr_remove($data, 0, 1);
            $encoded .= self::$encodingTable[$char] . '0';
        }
        
        $encoded = '10110010' . $encoded . '1011001';
        return $encoded;
    }
}