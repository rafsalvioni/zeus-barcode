<?php

namespace Zeus\Barcode;

/**
 * Implements a EAN13-ISSN barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml
 */
class ISSN extends Ean13
{
    /**
     * ISSN's barcodes is a EAN-13 beggining with 977.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        if (\strpos($data, '977') === 0) {
            return parent::checkData($data, $hasChecksum);
        }
        return false;
    }
}
