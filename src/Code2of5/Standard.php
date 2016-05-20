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
        $encoded = $this->widthToBinary('WWN');
        $i       = 0;
        $data    = \str_split($data);
        
        foreach ($data as &$char) {
            $encodedChar =& self::$encodingTable[$char];
            $encoded    .= $this->widthToBinary($encodedChar);
        }
        
        $encoded .= $this->widthToBinary('WNW');
        $encoded  = \substr_remove($encoded, 0, -$this->narrowWidth);
        return $encoded;
    }
    
    /**
     * Encodes a width char (ex. NWNWW) to a binary string using
     * the defined wide and narrow width.
     * 
     * @param string $nwChar
     * @return string
     */
    protected function widthToBinary($nwChar)
    {
        $encoded = '';
        while (!empty($nwChar)) {
            $nw       = \substr_remove($nwChar, 0, 1);
            $encoded .= $this->encodeWithWidth($nw == 'N', true);
            $encoded .= $this->encodeWithWidth(true, false);
        }
        return $encoded;
    }
}
