<?php

namespace Zeus\Barcode\Msi;

/**
 * Implements a MSI barcode standard using double mod10 checksum.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/msi.phtml
 */
class Msi2Mod10 extends AbstractMsiChecksum
{
    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $check  = self::mod10($data);
        $check .= self::mod10($data . $check);
        return $check;
    }
    
    /**
     * 
     * @return int
     */
    protected function getCheckPosition()
    {
        return -2;
    }
}
