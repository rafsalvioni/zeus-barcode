<?php

namespace Zeus\Barcode\Msi;

/**
 * Implements a MSI barcode standard using mod10 checksum.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/msi.phtml
 */
class MsiMod10 extends AbstractMsiChecksum
{
    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        return self::mod10($data);
    }
}
