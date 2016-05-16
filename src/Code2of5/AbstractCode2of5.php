<?php

namespace Zeus\Barcode\Code2of5;

use Zeus\Barcode\AbstractChecksumBarcode;
use Zeus\Barcode\TwoWidthInterface;
use Zeus\Barcode\TwoWidthTrait;

/**
 * Abstract class to implement Code2of5 barcodes.
 *
 * @author Rafael M. Salvioni
 */
abstract class AbstractCode2of5 extends AbstractChecksumBarcode implements
    TwoWidthInterface
{
    use TwoWidthTrait;

    /**
     * Encoding table
     * 
     * N -> narrow
     * W -> wide
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => 'NNWWN',
        '1' => 'WNNNW',
        '2' => 'NWNNW',
        '3' => 'WWNNN',
        '4' => 'NNWNW',
        '5' => 'WNWNN',
        '6' => 'NWWNN',
        '7' => 'NNNWW',
        '8' => 'WNNWN',
        '9' => 'NWNWN',
    ];

    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $data = \str_split($data);
        $sum  = self::sumAlternateWeight($data, 3, 1);
        $dv   = 10 - ($sum % 10);
        return $dv == 10 ? 0 : $dv;
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return preg_match('/^[0-9]{2,}$/', $data);
    }
}
