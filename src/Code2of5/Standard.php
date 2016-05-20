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
        $encoded = $this->widthToBinary('WNWNNN');
        $i       = 0;
        $data    = \str_split($data);
        
        foreach ($data as &$char) {
            $nwChar =& self::$encodingTable[$char];
            for ($i = 0; $i < 5; $i++) {
                $encoded .= $this->encodeWithWidth($nwChar{$i} == 'N', true);
                $encoded .= $this->encodeWithWidth(true, false);
            }
        }
        
        $encoded .= $this->widthToBinary('WNNNW');
        return $encoded;
    }
}
