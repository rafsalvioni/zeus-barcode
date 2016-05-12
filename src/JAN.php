<?php

namespace Zeus\Barcode;

/**
 * Implements a JAN barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml#JAN
 */
class JAN extends Ean13
{
    /**
     * JAN's barcodes is a EAN-13 beggining with 49.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        if (\strpos($data, '49') === 0) {
            return parent::checkData($data, $hasChecksum);
        }
        return false;
    }
}
