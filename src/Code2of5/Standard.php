<?php

namespace Zeus\Barcode\Code2of5;

/**
 * Implements a Standard/Industrial code 2 of 5 barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/2of5.phtml
 */
class Standard extends AbstractCode2of5
{
    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $encoded = '11011010';
        $i      = 0;
        $data   = \str_split($data);
        
        foreach ($data as &$char) {
            $encodedChar =& self::$encodingTable[$char];
            
            for ($j = 0; $j < 5; $j++) {
                $encoded .= $this->encodeWithWidth($encodedChar{$j} == 'N', true);
                $encoded .= $this->encodeWithWidth(true, false);
            }
        }
        
        $encoded .= '1101011';
        return $encoded;
    }
}
