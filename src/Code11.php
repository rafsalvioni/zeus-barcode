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
        '0' => '101011',  '1' => '1101011', '2' => '1001011', '3' => '1100101',
        '4' => '1011011', '5' => '1101101', '6' => '1001101', '7' => '1010011',
        '8' => '1101001', '9' => '110101',  '-' => '101101',
    ];
    
    /**
     * Checks if barcode data has a couple of digits of checksum
     * 
     * @var bool
     */
    private $doubleCheck = false;
    /**
     * Checks if the instance should be force a double checksum
     * 
     * @var bool
     */
    private $forceDoubleCheck;

    /**
     * $forceDoubleCheck will do that barcode checksum always have two digits
     * for checksum
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @param bool $forceDoubleCheck Force the calc of two checksums digits
     * @throws Exception If data or checksum is invalid
     */
    public function __construct(
        $data, $hasChecksum = true, $forceDoubleCheck = false
    ) {
        if (!$this->checkData($data, $hasChecksum)) {
            throw new Exception('Invalid barcode data chars or length!');
        }
        
        $this->forceDoubleCheck = (bool)$forceDoubleCheck;
        
        if ($hasChecksum) {
            $k = \substr_remove($data, -1);
            
            if (self::calcDigitK($data) == $k) {
                $this->doubleCheck = true;
            }
            else if (self::calcDigitC($data) == $k) {
                if ($this->forceDoubleCheck) {
                    $k .= self::calcDigitK($data . $k);
                    $this->doubleCheck = true;
                }
                else {
                    $this->doubleCheck = false;
                }
            }
            else {
                throw new Exception('Invalid barcode checksum!');
            }
            $this->data = $data . $k;
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
     * Always calculates "C" checksum and "K" checksum only if $data have
     * 10 or more chars.
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $check = self::calcDigitC($data);
        if ($this->forceDoubleCheck || \strlen($data) > 9) {
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
        $start     = $this->doubleCheck ? -2 : -1;
        $checksum  = \substr_remove($data, $start);
        $cleanData = $data;
        return $checksum;
    }
}
