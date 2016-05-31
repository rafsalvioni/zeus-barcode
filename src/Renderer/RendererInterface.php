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
     * Draws a polygon.
     * 
     * $points should be a points array with format:
     * [['x' => 0, 'y' => 0], ['x' => 10, 'y' => 10]]
     * 
     * If $points have only 2 points, draw a line. If only one, a single point.
     * 
     * @param array $points Points
     * @param int $color Color
     * @param bool $filled Polygon should be filled?
     * @return self
     */
    public function drawPolygon(array $points, $color, $filled = true);
    
    /**
     * Draws a text.
     * 
     * @param array $point Point
     * @param string $text Text
     * @param int $color Color
     * @param string $font Font
     * @param int $fontSize Font size
     * @return self
     */
    public function drawText(array $point, $text, $color, $font, $fontSize);

    /**
     * Returns the barcode text height. Use barcode options to calculate.
     * 
     * If $text was given, used it. If not, use the default printable barcode
     * data.
     * 
     * @return int
     */
    public function getTextHeight();
    
    /**
     * Returns the barcode text width. Use barcode options to calculate.
     * 
     * @param string $text
     * @return int
     */
    public function getTextWidth($text = null);
    
    /**
     * Returns the total draw width, including barcode, quietzones, border etc.
     * 
     * @return int
     */
    public function getTotalWidth();
    
    /**
     * Returns the total draw height, including barcode, border, text etc.
     * 
     * @return int
     */
    public function getTotalHeight();

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
    
    /**
     * Sets the initial resource. Depends of renderer.
     * 
     * @param mixed $resource
     * @return self
     */
    public function setResource($resource);
}
