<?php

namespace Zeus\Barcode\Code11;

/**
 * Implements a Code11 barcode standard using C checkdigit.
 * 
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code11.phtml
 */
class Code11C extends Code11
{
    /**
     * Creates a new barcode with K check digit.
     * 
     * @return Code11K
     */
    public function toDoubleCheck()
    {
        return new Code11K($this->getRawData(), false);
    }

    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        return self::calcDigitC($data);
    }
}
