<?php

namespace Zeus\Barcode\Renderer;

use Zeus\Barcode\BarcodeInterface;

/**
 * Trait to implement default methods and features of RendererInterface.
 *
 * @author Rafael M. Salvioni
 */
trait RendererTrait
{
    /**
     * Draw resource
     * 
     * @var mixed
     */
    protected $resource;
    /**
     * Barcode
     * 
     * @var BarcodeInterface
     */
    protected $barcode;

    /**
     * 
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Sets the barcode and call draw() method.
     * 
     * @param BarcodeInterface $barcode
     * @return self
     */
    public function setBarcode(BarcodeInterface $barcode)
    {
        if ($this->barcode !== $barcode) {
            $this->barcode = $barcode;
            $this->draw();
        }
        return $this;
    }
    
    /**
     * Draws the barcode to resource
     * 
     */
    abstract protected function draw();
    
    /**
     * Calculates the barcode width.
     * 
     * @return int
     */
    protected function calcBarcodeWidth()
    {
        $barcode =& $this->barcode;
        $width = $barcode->offsetLeft   +
                 $barcode->offsetRight  +
                 ($barcode->border * 2) +
                 $barcode->getWidth()   +
                 ($barcode->quietZone * 2);
        
        return (int)$width;
    }
    
    /**
     * Calculates the barcode height.
     * 
     * @return int
     */
    protected function calcBarcodeHeight()
    {
        $barcode =& $this->barcode;
        $height = $barcode->offsetTop +
                  $barcode->offsetBottom +
                  ($barcode->border * 2) +
                  $barcode->getHeight();
        
        return (int)$height;
    }
}
