<?php

namespace Zeus\Barcode\Msi;

/**
 * Implements a MSI barcode standard using mod11 checksum.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/msi.phtml
 */
class MsiMod11 extends AbstractMsiChecksum
{
    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        return self::mod11($data);
    }
}
