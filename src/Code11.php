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
    
    public function __construct($data, $hasChecksum = true)
    {
        if (!$this->checkData($data, $hasChecksum)) {
            throw new Exception('Invalid barcode data!');
        }
        if ($hasChecksum && \preg_match('/(.+?)(.)$/', $data, $match)) {
            $c = self::calcDigitC($match[1]);
            $k = self::calcDigitK($match[1]);
            
            if ($c == $match[2]) {
                $this->data     = $match[1];
                $this->checksum = $c;
            }
            else if ($k == $match[2]) {
                $this->data     = \substr($match[1], 0, -2);
                $this->checksum = \substr($match[1], -2);
            }
            else{
                throw new Exception('Invalid barcode checksum!');
            }
        }
        else {
            parent::__construct($data, false);
        }
    }

    /**
     * Calculates checkdigits using a max weight and modulus divisor.
     * 
     * @param array $data Splitted data
     * @param int $maxWeight
     * @param int $divisor
     * @return int
     */
    protected static function calcChecksumUsingWeight(
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
    
    protected static function calcDigitC($data)
    {
        return self::calcChecksumUsingWeight($data, 6, 11);
    }

    protected static function calcDigitK($data)
    {
        return self::calcChecksumUsingWeight($data, 7, 9);
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
        $check = self::calcDigitC($data);
        if (\strlen($data) > 9){
            $data  .= $check;
            $check .= self::calcDigitK($data);
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
        $mul = $hasChecksum ? '{2,}' : '+';
        return preg_match("/^[0-9\\-]$mul$/", $data);
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
