<?php

namespace Zeus\Barcode\Msi;

use Zeus\Barcode\AbstractBarcode;
use Zeus\Barcode\Encoder\EncoderInterface;

/**
 * Implements a MSI barcode standard, without checksum.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/msi.phtml
 */
class Msi extends AbstractBarcode
{
    /**
     * Encodign table
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => '100100100100', '1' => '100100100110', '2' => '100100110100',
        '3' => '100100110110', '4' => '100110100100', '5' => '100110100110',
        '6' => '100110110100', '7' => '100110110110', '8' => '110100100100',
        '9' => '110100100110',
    ];
    
    /**
     * Try to return the best Msi class according given parameters.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return Msi parent or subclasses
     * @throws \Zeus\Barcode\Exception
     */
    public static function factory($data, $hasChecksum = true)
    {
        if ($hasChecksum) {
            $classes = [
                MsiMod10::class, MsiMod11::class,
                Msi2Mod10::class, MsiMod1110::class,
            ];
            foreach ($classes as &$class) {
                try {
                    return new $class($data, true);
                }
                catch (\Exception $ex) {
                    //noop
                }
            }
            throw $ex;
        }
        else {
            return new self($data);
        }
    }
    
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
     * Create a new instance using checksum mod10.
     * 
     * @return MsiMod10
     */
    public function withChecksumMod10()
    {
        return $this->toClass(MsiMod10::class);
    }

    /**
     * Create a new instance using checksum mod11.
     * 
     * @return MsiMod11
     */
    public function withChecksumMod11()
    {
        return $this->toClass(MsiMod11::class);
    }

    /**
     * Create a new instance using checksum double mod10.
     * 
     * @return Msi2Mod10
     */
    public function withChecksum2Mod10()
    {
        return $this->toClass(Msi2Mod10::class);
    }

    /**
     * Create a new instance using checksum mod11 + mod10.
     * 
     * @return MsiMod1110
     */
    public function withChecksumMod1110()
    {
        return $this->toClass(MsiMod1110::class);
    }
    
    /**
     * Converts current barcode to another barcode of MSI family.
     * 
     * @param string $class Msi class
     * @return Msi
     */
    protected function toClass($class)
    {
        $data = null;
        if ($this instanceof AbstractMsiChecksum) {
            $this->extractChecksum($this->data, $data);
        }
        else {
            $data = $this->data;
        }
        return new $class($data, false);
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return \preg_match('/^[0-9]+$/', $data);
    }

    /**
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    protected function encodeData(EncoderInterface &$encoder, $data)
    {
        $n = \strlen($data);
        $encoder->addBinary('110');
        
        for ($i = 0; $i < $n; $i++) {
            $encoded = self::$encodingTable[$data{$i}];
            $encoder->addBinary($encoded);
        }
        
        $encoder->addBinary('1001');
    }
}
