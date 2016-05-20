<?php

namespace Zeus\Barcode\Code2of5;

use Zeus\Barcode\Encoder\BarSpace;

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
     * @param BarSpace $encoder
     * @param string $data
     */
    protected function encodeData(BarSpace &$encoder, $data)
    {
        $data    = \str_split($data);
        $encoded = $this->widthToBinary('WNWNNN');
        $encoder->addBinary($encoded);
        
        foreach ($data as &$char) {
            $nwChar =& self::$encodingTable[$char];
            for ($i = 0; $i < 5; $i++) {
                $encoded  = $this->encodeWithWidth($nwChar{$i} == 'N', true);
                $encoded .= $this->encodeWithWidth(true, false);
                $encoder->addBinary($encoded);
            }
        }
        
        $encoded = $this->widthToBinary('WNNNW');
        $encoder->addBinary($encoded);
    }
}
