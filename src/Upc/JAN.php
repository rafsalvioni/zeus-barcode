<?php

namespace Zeus\Barcode\Upc;

/**
 * Implements a JAN barcode standard.
 * 
 * Is a EAN-13 barcode using on Japan.
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
     * @return bool
     */
    protected function checkData($data)
    {
        if (\strpos($data, '49') === 0) {
            return parent::checkData($data);
        }
        return false;
    }
}