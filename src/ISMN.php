<?php

namespace Zeus\Barcode;

/**
 * Implements a EAN13-ISMN barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml
 */
class ISMN extends Ean13
{
    /**
     * ISMN's barcodes is a EAN-13 beggining with 979.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        if (\strpos($data, '979') === 0) {
            return parent::checkData($data, $hasChecksum);
        }
        return false;
    }
}
