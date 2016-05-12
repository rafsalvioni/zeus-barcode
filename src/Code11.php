<?php

namespace Zeus\Barcode;

/**
 * Implements a barcode Code11 standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code11.phtml
 */
class Code11 extends AbstractBarcode
{
    /**
     * Encoding table
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => '101011',
        '1' => '1101011',
        '2' => '1001011',
        '3' => '1100101',
        '4' => '1011011',
        '5' => '1101101',
        '6' => '1001101',
        '7' => '1010011',
        '8' => '1101001',
        '9' => '110101',
        '-' => '101101',
    ];
    
    /**
     * Calculates checkdigits using a max weight and modulus divisor.
     * 
     * @param array $data Splitted data
     * @param int $maxWeight
     * @param int $divisor
     * @return int
     */
    protected static function calcChecksumUsingWeight(
        array $data, $maxWeight, $divisor
    ) {
        $sum    = 0;
        $weight = 1;
        
        while (!empty($data)) {
            $num = \array_pop($data);
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
     * Always calculates "C" checksum and "K" checksum only if $data have
     * 10 or more chars.
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $data  = \str_split($data);
        $check = self::calcChecksumUsingWeight($data, 6, 11);
        if (\count($data) > 9) {
            $data[] = $check;
            $check .= self::calcChecksumUsingWeight($data, 7, 9);
        }
        return $check;
    }

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        return preg_match('/^[0-9\\-]+$/', $data);
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
    
    /**
     * If $data has more than 10 chars, get C and K. Otherwise, only C.
     * 
     * @param string $data
     * @param mixed $cleanData
     * @return string
     */
    protected function extractChecksum($data, &$cleanData)
    {
        $start     = \strlen($data) < 10 ? -1 : -2;
        $checksum  = \substr_remove($data, $start);
        $cleanData = $data;
        return $checksum;
    }
}
