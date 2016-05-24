<?php

namespace Zeus\Barcode;

/**
 * Implementation of ITF-14 barcode standard.
 * 
 * ITF-14 is a Interleaved 2 of 5 barcode with 14 fixed digits length
 * and variable length fields.
 * 
 * A ITF-14 barcode data should begin with a digit between 0 and 8.
 *
 * @author Rafael M. Salvioni
 * @see http://www.gtin.info/itf-14-barcodes/
 */
class Itf14 extends Code2of5\Interleaved implements FixedLengthInterface
{
    /**
     * Always 14.
     * 
     * @return int
     */
    public function getLength()
    {
        return 14;
    }
    
    /**
     * 
     * @param string $data
     * @return bool
     */
    public function checkData($data)
    {
        $len = $this->getLength();
        if (\strlen($data) == $len && $data{0} != '9') {
            return parent::checkData($data);
        }
        return false;
    }
}
