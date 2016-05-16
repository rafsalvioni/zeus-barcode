<?php

namespace Zeus\Barcode;

/**
 * Implements a EAN-5 supplemental barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/upcext.phtml
 */
class Ean5 extends AbstractBarcode
{
    use EanHelperTrait;
    
    /**
     * Parity table
     * 
     * 0 => Odd
     * 1 => Even
     * 
     * @var array
     */
    protected static $parityTable = [
        '0' => [1, 1, 0, 0, 0], '1' => [1, 0, 1, 0, 0],
        '2' => [1, 0, 0, 1, 0], '3' => [1, 0, 0, 0, 1],
        '4' => [0, 1, 1, 0, 0], '5' => [0, 0, 1, 1, 0],
        '6' => [0, 0, 0, 1, 1], '7' => [0, 1, 0, 1, 0],
        '8' => [0, 1, 0, 0, 1], '9' => [0, 0, 1, 0, 1],
    ];
    
    /**
     * Padding zeros left on $data to complete the necessary length.
     * 
     * @param string $data
      */
    public function __construct($data)
    {
        $data = self::zeroLeftPadding($data, 5);
        parent::__construct($data);
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return \preg_match("/^[0-9]{5}$/", $data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $check     = \substr(self::calcSumCheck($data, 3, 9), -1);
        $parityTab =& self::$parityTable[$check];
        $encoded   = '1011';
                    
        for ($i = 0; $i < 5; $i++) {
            $encoded .= self::$encodingTable[$data{$i}][$parityTab[$i]];
            $encoded .= '01';
        }
        
        $encoded   = \substr($encoded, 0, -2);
        return $encoded;
    }
}
