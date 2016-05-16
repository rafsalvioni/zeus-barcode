<?php

namespace Zeus\Barcode;

/**
 * Helper trait to use in barcodes derived from UPC/EAN.
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
     * Returns a checksum basead on modulus 10.
     * 
     * @param string $data
     * @return int
     */
    protected static function checkSumMod10($data)
    {
        $sum = self::calcSumCheck($data);
        $d   = 10 - ($sum % 10);
        return $d == 10 ? 0 : $d;
    }
}
