<?php

namespace Zeus\Barcode\Code2of5;

use Zeus\Barcode\BarSet;

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
     * @param BarSet $bars
     * @param string $data
     */
    protected function encodeData(BarSet &$bars, $data)
    {
        $data    = \str_split($data);
        $encoded = $this->widthToBinary('WNWNNN');
        $bars->addBinary($encoded);
        
        foreach ($data as &$char) {
            $nwChar =& self::$encodingTable[$char];
            for ($i = 0; $i < 5; $i++) {
                $encoded  = $this->encodeWithWidth($nwChar{$i} == 'N', true);
                $encoded .= $this->encodeWithWidth(true, false);
                $bars->addBinary($encoded);
            }
        }
        
        $encoded = $this->widthToBinary('WNNNW');
        $bars->addBinary($encoded);
    }
}
