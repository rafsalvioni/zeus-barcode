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
        '0' => [0, 0], '1' => [0, 1], '2' => [1, 0], '3' => [1, 1],
    ];
    
    /**
     * Padding zeros left on $data to complete the necessary length.
     * 
     * @param string $data
     */
    public function __construct($data)
    {
        $data = self::zeroLeftPadding($data, 2);
        parent::__construct($data);
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
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
