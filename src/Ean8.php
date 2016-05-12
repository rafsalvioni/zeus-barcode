<?php

namespace Zeus\Barcode;

/**
 * Implements a EAN8 barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean8.phtml
 */
class Ean8 extends AbstractBarcode
{
    /**
     * Encoding table, with parity
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => ['0001101', '1110010'],
        '1' => ['0011001', '1100110'],
        '2' => ['0010011', '1101100'],
        '3' => ['0111101', '1000010'],
        '4' => ['0100011', '1011100'],
        '5' => ['0110001', '1001110'],
        '6' => ['0101111', '1010000'],
        '7' => ['0111011', '1000100'],
        '8' => ['0110111', '1001000'],
        '9' => ['0001011', '1110100'],
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
            $weight = ($i % 2) == 0 ? 3 : 1;
            $sum   += (int)$num * $weight;
        }
        
        $d = 10 - ($sum % 10);
        return $d;
    }

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        $len = 8;
        if (!$hasChecksum) {
            $len--;
        }
        return \preg_match("/^[0-9]{{$len}}$/", $data);
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
            $parity   = $i < 4 ? 0 : 1;
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
