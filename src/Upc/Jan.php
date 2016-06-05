<?php

namespace Zeus\Barcode\Upc;

/**
 * Implements a EAN13-JAN barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml#JAN
 */
class Jan extends Ean13
{
    /**
     * JAN barcode should be begin with 45 or 49
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        if (\preg_match('/^4[59]/', $data)) {
            return parent::checkData($data);
        }
        return false;
    }
}
