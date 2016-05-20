<?php

namespace Zeus\Barcode\Code2of5;

use Zeus\Barcode\Encoder\BarSpace;

/**
 * Implements a Interleaved 2 of 5 barcode standard.
 * 
 * This barcode support only numeric chars and the length should be even.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/int2of5.phtml
 */
class Interleaved extends AbstractCode2of5
{
    /**
     * Padding data with zeros left to make length even, if necessary.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @throws Exception
     */
    public function __construct($data, $hasChecksum = true)
    {
        $len = \strlen($data);
        if (($len % 2) == 0) {
            $len += $hasChecksum ? 0 : 1;
        }
        else {
            $len += $hasChecksum ? 1 : 0;
        }
        $data = self::zeroLeftPadding($data, $len);
        parent::__construct($data, $hasChecksum);
        $this->wideWidth = 2;
    }

    /**
     * 
     * @param BarSpace $encoder
     * @param string $data
     */
    protected function encodeData(BarSpace &$encoder, $data)
    {
        $data    = \str_split($data);
        $encoded = $this->widthToBinary('NNNN');
        $encoder->addBinary($encoded);
        
        while (!empty($data)) {
            $nwChar1 =& self::$encodingTable[\array_shift($data)];
            $nwChar2 =& self::$encodingTable[\array_shift($data)];
            
            for ($i = 0; $i < 5; $i++) {
                $encoded  = $this->encodeWithWidth($nwChar1{$i} == 'N', true);
                $encoded .= $this->encodeWithWidth($nwChar2{$i} == 'N', false);
                $encoder->addBinary($encoded);
            }
        }
        
        $encoded = $this->widthToBinary('WNN');
        $encoder->addBinary($encoded);
    }
}
