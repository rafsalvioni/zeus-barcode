<?php

namespace Zeus\Barcode\DHL;

/**
 * Implementation of Leitcode barcode standard.
 * 
 * Leitcode is a Interleaved 2 of 5 barcode with 14 fixed digits length.
 * 
 * @author Rafael M. Salvioni
 * @see http://www.activebarcode.com/codes/leitcode.html
 */
class Leitcode extends AbstractDHL
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
}
