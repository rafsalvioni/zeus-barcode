<?php

namespace Zeus\Barcode\Code11;

/**
 * Implements a Code11 barcode standard using C and K checkdigit.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code11.phtml
 */
class Code11K extends Code11
{
    /**
     * Creates a new barcode with only C check digit.
     * 
     * @return Code11C
     */
    public function toSingleCheck()
    {
        return new Code11C(\substr($this->data, 0, -1));
    }

    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $check  = self::calcDigitC($data);
        $check .= self::calcDigitK($data . $check);
        return $check;
    }
    
    /**
     * 
     * @return int
     */
    protected function getCheckPosition()
    {
        return -2;
    }
    
    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        if (parent::checkData($data)) {
            return $this->checkLength($data);
        }
        return false;
    }
}
