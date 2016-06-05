<?php

namespace Zeus\Barcode;

/**
 * Implementation of Leitcode barcode standard.
 * 
 * Leitcode is a Interleaved 2 of 5 barcode with 14 fixed digits length
 * and variable length fields.
 * 
 * @author Rafael M. Salvioni
 * @see http://www.activebarcode.com/codes/leitcode.html
 */
class Leitcode extends Code2of5\Interleaved implements FixedLengthInterface
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
     * @return string
     */
    public function getDataToDisplay()
    {
        return \preg_replace(
                    '/^(\d{5})(\d{3})(\d{3})(\d{2})(\d)$/',
                    '$1.$2.$3.$4 $5',
                    $this->data
                );
    }

    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $data  = \str_split($data);
        $sum   = self::sumAlternateWeight($data, 4, 9);
        $check = (10 - ($sum % 10)) % 10;
        return $check;
    }
}
