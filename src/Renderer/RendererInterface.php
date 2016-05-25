<?php

namespace Zeus\Barcode\Renderer;

/**
 * Identifies a barcode renderer.
 * 
 * @author Rafael M. Salvioni
 */
interface RendererInterface
{
    /**
     * Notifies the renderer that a draw will start.
     * 
     * @param int $width Width of barcode draw
     * @param int $height Height of barcode draw
     * @return self
     */
    public function startResource($width, $height = 1);

    /**
     * Draws a bar/space.
     * 
     * @param bool $bar Is a bar?
     * @param number $width Bar's width factor
     * @param number $height Bar's height factor
     * @param number $posy Vertical position factor
     * @return self
     */
    public function drawBar($bar, $width = 1, $height = 1, $posy = 0);
    
    /**
     * Writes a text.
     * 
     * @param string $text
     * @param int $size
     * @param number $posy Vertical position factor
     * @return self
     */
    public function writeText($text, $size = 1, $posy = 0);
    
    /**
     * Returns the bar width used by renderer.
     * 
     * @return int 
     */
    public function getBarWidth();
    
    /**
     * Returns the bar height used by renderer.
     * 
     * @return int
     */
    public function getBarHeight();

    /**
     * Returns the draw's resource.
     * 
     * @return mixed
     */
    public function getResource();
    
    /**
     * Shows the barcode's draw
     * 
     */
    public function show();
}
