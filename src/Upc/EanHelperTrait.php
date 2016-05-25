<?php

namespace Zeus\Barcode\Upc;

/**
 * Trait to be used on UPC/EAN classes that implements
 * \Zeus\Barcode\FixedLengthInterface and \Zeus\Barcode\ChecksumInterface
 * interfaces.
 *
 * @author Rafael M. Salvioni
 */
trait EanHelperTrait
{
    /**
     * Encoding table, with parity
     * 
     * [Odd, Even, Right]
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => ['0001101', '0100111', '1110010'],
        '1' => ['0011001', '0110011', '1100110'],
        '2' => ['0010011', '0011011', '1101100'],
        '3' => ['0111101', '0100001', '1000010'],
        '4' => ['0100011', '0011101', '1011100'],
        '5' => ['0110001', '0111001', '1001110'],
        '6' => ['0101111', '0000101', '1010000'],
        '7' => ['0111011', '0010001', '1000100'],
        '8' => ['0110111', '0001001', '1001000'],
        '9' => ['0001011', '0010111', '1110100'],
    ];
    
    /**
     * Returns the system digit of a Ean13 data.
     * 
     * @param string $data
     * @return string
     */
    protected static function getSystemDigits($data)
    {
        if (\strpos($data, '99') === 0) {
            return '99';
        }
        return \substr($data, 0, 3);
    }

    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $data = \str_split($data);
        $sum  = self::sumAlternateWeight($data, 3, 1);
        $d    = 10 - ($sum % 10);
        return $d == 10 ? 0 : $d;
    }
    
    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        $len = (int)$this->getLength();
        return preg_match("/^[0-9]{{$len}}$/", $data);
    }
}
