<?php

namespace Zeus\Barcode;

/**
 * Implements a barcode using 2 of 5 interleaved standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/int2of5.phtml
 */
class Interleaved25 extends AbstractBarcode
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
     * Convert a W or N bar to a binary string.
     * 
     * @param string $bar
     * @param bool $black
     * @return string
     */
    private static function barToBin($bar, $black)
    {
        $r = $bar == 'W' ? 2 : 1;
        return \str_repeat($black ? '1' : '0', $r);
    }

    /**
     * Padding zeros left on $data to complete the necessary length.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        $len = \strlen($data);
        if (($len % 2) == 0) {
            $len += !$hasChecksum ? 1 : 0;
        }
        else {
            $len +=  $hasChecksum ? 1 : 0;
        }
        $data = self::zeroLeftPadding($data, $len);
        parent::__construct($data, $hasChecksum);
    }
    
    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        // On constructor, data length even or odd is adjusted
        $mul = $hasChecksum ? '{2,}' : '+';
        return \preg_match("/^[0-9]{$mul}$/", $data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $encoded = '1010';
        $data    = \str_split($data);
        
        while (!empty($data)) {
            $char1 =& self::$encodingTable[\array_shift($data)];
            $char2 =& self::$encodingTable[\array_shift($data)];
            
            for ($i = 0; $i < 5; $i++) {
                $encoded .= self::barToBin($char1{$i}, true);
                $encoded .= self::barToBin($char2{$i}, false);
            }
        }
        
        $encoded .= '1101';
        return $encoded;
    }

    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        return (new Industrial25($data, false))->getChecksum();
    }
}
