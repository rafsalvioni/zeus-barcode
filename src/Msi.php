<?php

namespace Zeus\Barcode;

/**
 * Implements a MSI barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/msi.phtml
 */
class Msi extends AbstractChecksumBarcode
{
    /**
     * Encodign table
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => '100100100100',
        '1' => '100100100110',
        '2' => '100100110100',
        '3' => '100100110110',
        '4' => '100110100100',
        '5' => '100110100110',
        '6' => '100110110100',
        '7' => '100110110110',
        '8' => '110100100100',
        '9' => '110100100110',
    ];
    
    /**
     * Stores the length of checksum
     * 
     * @var int
     */
    protected $digitsCheck;
    /**
     * Stores the function that calculate checksum
     * 
     * @var string
     */
    protected $checkFunction;

    /**
     * Checksum mod10.
     * 
     * @param string $data
     * @return int
     */
    protected static function mod10($data)
    {
        $data = \str_split($data);
        $last = \array_pop($data) * 2;
        $sum  = \array_sum($data) + $last;
        $mod  = ($sum % 10);
        $cd   = 10 - $mod;
        return $cd == 10 ? 0 : $cd;
    }

    /**
     * Checksum mod11.
     * 
     * @param string $data
     * @return int
     */
    protected static function mod11($data)
    {
        $data = \str_split($data);
        $sum  = self::sumCrescentWeight($data, 2);
        $cd   = 11 - ($sum % 11);
        return $cd == 11 ? 0 : $cd;
    }
    
    /**
     * Checksum double mod10.
     * 
     * @param string $data
     * @return int
     */
    protected static function doubleMod10($data)
    {
        $check  = self::mod10($data);
        $check .= self::mod10($data . $check);
        return $check;
    }
    
    /**
     * Checksum mod11 + mod10.
     * 
     * @param string $data
     * @return int
     */
    protected static function mod1110($data)
    {
        $check  = self::mod11($data);
        $check .= self::mod10($data . $check);
        return $check;
    }
    
    /**
     * MSI supports many ways to calculate checksum digits. The class
     * supports all of them and it will try the better way.
     * 
     * If $hasChecksum is false, checksum will be calculated using modulus 11
     * method.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        if (!$this->checkData(!$hasChecksum ? $data . '0' : $data)) {
            throw $this->createException('Invalid "%class%" barcode data chars or length!');
        }

        $this->digitsCheck   = 1;
        $this->checkFunction = 'mod11';

        if ($hasChecksum) {
            $cd = \substr_remove($data, -2);
            
            switch (true) {
                case self::mod1110($data) == $cd:
                    $this->digitsCheck   = 2;
                    $this->checkFunction = 'mod1110';
                    break;
                case self::doubleMod10($data) == $cd:
                    $this->digitsCheck   = 2;
                    $this->checkFunction = 'doubleMod10';
                    break;
                case self::mod11($data . $cd{0}) == $cd{1}:
                    $this->digitsCheck   = 1;
                    $this->checkFunction = 'mod11';
                    break;
                case self::mod10($data . $cd{0}) == $cd{1}:
                    $this->digitsCheck   = 1;
                    $this->checkFunction = 'mod10';
                    break;
                default:
                    throw $this->createException('Invalid "%class%" barcode checksum!');
            }
            $this->data = $data . $cd;
        }
        else {
            parent::__construct($data, false);
        }
    }
    
    /**
     * Create a new instance using checksum mod10.
     * 
     * @return Msi
     */
    public function toMod10()
    {
        $this->extractChecksum($this->data, $data);
        $check = self::mod10($data);
        return new self($data . $check);
    }

    /**
     * Create a new instance using checksum mod11.
     * 
     * @return Msi
     */
    public function toMod11()
    {
        $this->extractChecksum($this->data, $data);
        $check = self::mod11($data);
        return new self($data . $check);
    }

    /**
     * Create a new instance using checksum double mod10.
     * 
     * @return Msi
     */
    public function toDoubleMod10()
    {
        $this->extractChecksum($this->data, $data);
        $check = self::doubleMod10($data);
        return new self($data . $check);
    }

    /**
     * Create a new instance using checksum mod11 + mod10.
     * 
     * @return Msi
     */
    public function toMod1110()
    {
        $this->extractChecksum($this->data, $data);
        $check = self::mod1110($data);
        return new self($data . $check);
    }

    /**
     * Calculate the checksum using the function defined on $checkFunction
     * property.
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        return self::{$this->checkFunction}($data);
    }
    
    /**
     * Extract the checksum using $digitsCheck property.
     * 
     * @param string $data
     * @param mixed $cleanData
     * @return string
     */
    protected function extractChecksum($data, &$cleanData)
    {
        $checksum  = \substr_remove($data, -$this->digitsCheck);
        $cleanData = $data;
        return $checksum;
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return \preg_match('/^[0-9]{2,}$/', $data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $encoded = '110';
        $n       = \strlen($data);
        
        for ($i = 0; $i < $n; $i++) {
            $encoded .= self::$encodingTable[$data{$i}];
        }
        
        $encoded .= '1001';
        return $encoded;
    }
}
