<?php

namespace Zeus\Barcode\Upc;

/**
 * Implements a EAN13-ISBN barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml
 */
class ISBN extends Ean13
{
    /**
     * Create a barcode instance from a ISBN code.
     * 
     * @param string $isbn
     * @return self
     */
    public static function fromISBN($isbn)
    {
        $isbn = \preg_replace('/[^\d]/', '', $isbn);
        $isbn = \substr($isbn, 0, -1);
        return new self('978' . $isbn, false);
    }

    /**
     * ISBN's barcodes is a EAN-13 beggining with 978.
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        if (\strpos($data, '978') === 0) {
            return parent::checkData($data);
        }
        return false;
    }
}
