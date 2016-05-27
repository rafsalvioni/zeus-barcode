<?php

namespace Zeus\Barcode\Renderer;

use Zeus\Barcode\BarcodeInterface;

/**
 * Identifies a barcode renderer.
 * 
 * @author Rafael M. Salvioni
 */
interface RendererInterface
{
    /**
     * Sets the barcode to draw/render.
     * 
     * @param BarcodeInterface $barcode
     * @return self
     */
    public function setBarcode(BarcodeInterface $barcode);
    
    /**
     * Render the barcode to output with its own headers.
     * 
     */
    public function render();
    
    /**
     * Return the barcode resource. Depends of renderer.
     * 
     */
    public function getResource();
}
