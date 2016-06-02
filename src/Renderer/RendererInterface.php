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
     * Start a barcode draw.
     * 
     * @param BarcodeInterface $barcode
     * @return self
     */
    public function start(BarcodeInterface $barcode);

    /**
     * Draws a rectangle.
     * 
     * $points should be a points array with format (X, Y):
     * [[0, 0], [10, 10]]
     * 
     * @param array $points Points
     * @param int $color Color
     * @param bool $filled Rect should be filled?
     * @return self
     */
    public function drawRect(array $points, $color, $filled = true);
    
    /**
     * Draws a text.
     * 
     * @param array $point Point, same format of drawRect()
     * @param string $text Text
     * @param int $color Color
     * @param string $font Font
     * @param int $fontSize Font size
     * @param string $align
     * @return self
     */
    public function drawText(
        array $point, $text, $color, $font, $fontSize, $align = null
    );

    /**
     * Render the resource to output with its own headers.
     * 
     */
    public function render();
    
    /**
     * Return the renderer resource. Depends of renderer.
     * 
     */
    public function getResource();
    
    /**
     * Sets the initial resource. Depends of renderer.
     * 
     * "merge" option should be defined with true.
     * 
     * @param mixed $resource
     * @return self
     */
    public function setResource($resource);
}
