<?php

namespace Zeus\Barcode\Renderer;

/**
 *
 * @author Rafael M. Salvioni
 */
interface RendererInterface
{
    const TEXT_POSITION_TOP    = 'top';
    const TEXT_POSITION_BOTTOM = 'bottom';
    const TEXT_POSITION_NONE   = '';
    const TEXT_ALIGN_LEFT      = 'left';
    const TEXT_ALIGN_RIGHT     = 'right';
    const TEXT_ALIGN_CENTER    = 'center';
    
    public function resetDraw();

    public function drawBar($black, $width = 1, $height = 1);
    
    public function setText($text);
    
    public function setTextPosition($position);
    
    public function setTextAlign($align);
    
    public function setBarWidth($width);
    
    public function setBarHeight($height);

    public function getResource();
}
