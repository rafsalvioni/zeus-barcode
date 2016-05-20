<?php

namespace Zeus\Barcode;

use Zeus\Barcode\AbstractChecksumBarcode;

/**
 * Implements a Postnet barcode standard.
 * 
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/postnet.phtml
 */
class Postnet extends AbstractChecksumBarcode
{
    /**
     * Encoding table
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => '11000', '1' => '00011', '2' => '00101',
        '3' => '00110', '4' => '01001', '5' => '01010',
        '6' => '01100', '7' => '10001', '8' => '10010',
        '9' => '10100',
    ];

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function calcChecksum($data)
    {
        $data  = \str_split($data);
        $sum   = self::sumAlternateWeight($data, 1, 1);
        $check = 10 - ($sum % 10);
        return $check == 10 ? 0 : $check;
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return \preg_match('/^\d{2,}$/', $data);
    }

    /**
     * 
     * @return int
     */
    protected function getCheckPosition()
    {
        return -1;
    }

    /**
     * 
     * @param BarSet $bars
     * @param string $data
     */
    protected function encodeData(BarSet &$bars, $data)
    {
        $bars->addBar()->addSpace();
        $data = \str_split($data);
        
        foreach ($data as &$char) {
            $encoded =& self::$encodingTable[$char];
            for ($i = 0; $i < 5; $i++) {
                $height = $encoded{$i} == '1' ? 1 : 0.5;
                $bars->addBar(1, $height);
                $bars->addSpace();
            }
        }
        
        $bars->addBar();
    }
}
