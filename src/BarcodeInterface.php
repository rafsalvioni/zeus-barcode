<?php

namespace Zeus\Barcode;

use Zeus\Barcode\Renderer\RendererInterface;

/**
 * Identifies a barcode object.
 * 
 * @author Rafael M. Salvioni
 */
interface BarcodeInterface
{
    /**
     * Returns the barcode data, without or without checksum, if has one.
     * 
     * @param bool $withChecksum
     * @return string
     */
    public function getData($withChecksum = false);
    
    /**
     * Returns the barcode checksum, if has one.
     * 
     * @return int
     */
    public function getChecksum();
    
    /**
     * Returns the barcode data encoded with 0 or 1. "0" represents a white
     * bar and "1" a black bar.
     * 
     * @return string
     */
    public function getEncoded();
    
    /**
     * Returns the barcode data to be printed.
     * 
     * @return string
     */
    public function getPrintableData();

    /**
     * Draw the barcode on a renderer.
     * 
     * Returns the own renderer.
     * 
     * @param RendererInterface $renderer
     * @return RendererInterface
     */
    public function render(RendererInterface $renderer);
}
