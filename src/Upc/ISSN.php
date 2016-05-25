<?php

namespace Zeus\Barcode\Upc;

/**
 * Implements a EAN13-ISSN barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml
 */
class ISSN extends Ean13
{
    /**
     * EAN-13 ISSN system digits
     * 
     */
    const SYSTEM = '977';

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
        $issn = self::SYSTEM . \substr($issn, 0, -1) . '00';
        return new self($issn, false);
    }

    /**
     * ISSN's barcodes is a EAN-13 beggining with 977.
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        if (\strpos($data, self::SYSTEM) === 0) {
            return parent::checkData($data);
        }
        return false;
    }
}
