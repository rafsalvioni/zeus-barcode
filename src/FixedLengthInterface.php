<?php

namespace Zeus\Barcode;

/**
 * Identifies a fixed length barcode.
 * 
 * @author Rafael M. Salvioni
 */
interface FixedLengthInterface extends BarcodeInterface
{
    /**
     * Returns the barcode length.
     * 
     * @return int
     */
    public function getLength();
}
