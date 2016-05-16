<?php

namespace Zeus\Barcode;

/**
 * Implements a EAN-2 supplemental barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/upcext.phtml
 */
class Ean2 extends AbstractBarcode
{
    /**
     * Parity table
     * 
     * 0 => Odd
     * 1 => Even
     * 
     * @var array
     */
    protected static $parityTable = [
        '0' => [0, 0], '1' => [0, 1], '2' => [1, 0], '3' => [1, 1],
    ];
    
    /**
     * Encoding table, with parity
     * 
     * [Odd, Even]
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => ['0001101', '0100111'], '1' => ['0011001', '0110011'],
        '2' => ['0010011', '0011011'], '3' => ['0111101', '0100001'],
        '4' => ['0100011', '0011101'], '5' => ['0110001', '0111001'],
        '6' => ['0101111', '0000101'], '7' => ['0111011', '0010001'],
        '8' => ['0110111', '0001001'], '9' => ['0001011', '0010111'],
    ];
    
    /**
     * Padding zeros left on $data to complete the necessary length.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        $data = self::zeroLeftPadding($data, 2);
        parent::__construct($data, false);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function calcChecksum($data)
    {
        return null;
    }
    
    /**
     * 
     * @param string $data
     * @param mixed $cleanData
     * @return string
     */
    protected function extractChecksum($data, &$cleanData)
    {
        $cleanData = $data;
        return '';
    }
    
    /**
     * 
     * @param string $data
     * @param string $checksum
     * @return string
     */
    protected function insertChecksum($data, $checksum)
    {
        return $data;
    }

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        return \preg_match("/^[0-9]{2}$/", $data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $encoded   = '';
        $parityTab =& self::$parityTable[($data % 4)];
        
        $encoded  = '1011' .
                    self::$encodingTable[$data{0}][$parityTab[0]] .
                    '01' .
                    self::$encodingTable[$data{1}][$parityTab[1]];
        
        return $encoded;
    }
}
