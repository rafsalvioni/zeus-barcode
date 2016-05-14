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
     * Create a instance using a ISSN number.
     * 
     * ISSN has a format like be XXXX-XXXX
     * 
     * @param string $issn
     * @return ISSN
     */
    public static function fromISSN($issn)
    {
        $issn = \preg_replace('/[^\d]/', '', $issn);
        $issn = '977' . \substr($issn, 0, -1) . '00';
        return new self($issn, false);
    }

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
