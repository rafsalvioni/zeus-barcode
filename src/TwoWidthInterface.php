<?php

namespace Zeus\Barcode;

/**
 * Identifies barcodes that using two widths.
 * 
 * These barcodes using a concept of narrow and wide bars or spaces and, for
 * this reason, we can configure the widths of narrow and wide.
 * 
 * If a barcode don't implement this interface, so it is a multiple width
 * barcode.
 * 
 * @author Rafael M. Salvioni
 */
interface TwoWidthInterface extends BarcodeInterface
{
    /**
     * Define the width of wide bars or spaces.
     * 
     * Should be greater than zero and narrow width.
     * 
     * @param int $width
     * @return self
     */
    public function setWideWidth($width);
    
    /**
     * Define the width of narrow bars or spaces.
     * 
     * Should be greater than zero and less than wide width.
     * 
     * @param int $width
     * @return self
     */
    public function setNarrowWidth($width);
}
