<?php

namespace Zeus\Barcode\DHL;

/**
 * Implementation of Identcode barcode standard.
 * 
 * Leitcode is a Interleaved 2 of 5 barcode with 12 fixed digits.
 * 
 * @author Rafael M. Salvioni
 * @see http://www.activebarcode.com/codes/identcode.html
 */
class Identcode extends AbstractDHL
{
    /**
     * Always 14.
     * 
     * @return int
     */
    public function getLength()
    {
        return 12;
    }
    
    /**
     * 
     * @return string
     */
    public function getDataToDisplay()
    {
        return \preg_replace(
                    '/^(\d{2})(\d{3})(\d{3})(\d{3})(\d)$/',
                    '$1.$2 $3.$4 $5',
                    $this->data
                );
    }
}
