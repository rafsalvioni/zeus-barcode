<?php

namespace Zeus\Barcode\Renderer;

use Zeus\Barcode\BarcodeInterface;
use Zeus\Barcode\OptionsTrait;

/**
 * Trait to implement default methods and features of RendererInterface.
 *
 * @author Rafael M. Salvioni
 */
trait RendererTrait
{
    use OptionsTrait;
    
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
            $this->initResource();
        }
        return $this;
    }
    
    /**
     * Calculates the barcode width.
     * 
     * @return int
     */
    public function getTotalWidth()
    {
        $barcode =& $this->barcode;
        $width = ($barcode->border * 2) +
                 $barcode->getWidth()   +
                 ($barcode->quietZone * 2);
        
        return (int)$width;
    }
    
    /**
     * Calculates the barcode height.
     * 
     * @return int
     */
    public function getTotalHeight()
    {
        $barcode =& $this->barcode;
        $height = ($barcode->border * 2) +
                  $barcode->getHeight();
        
        if ($barcode->showText) {
            $height += $this->getTextHeight();
        }
        
        return (int)$height;
    }
    
    /**
     * Initializes the barcode to resource
     * 
     */
    abstract protected function initResource();
}
