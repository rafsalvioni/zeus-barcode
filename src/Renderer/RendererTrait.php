<?php

namespace Zeus\Barcode\Renderer;

/**
 * Description of RendererTrait
 *
 * @author rafaelsalvioni
 */
trait RendererTrait
{
    protected $barHeight;
    protected $barWidth;
    
    public function __construct($barWidth = 2, $barHeight = 60)
    {
        $this->barHeight = $barHeight;
        $this->barWidth  = $barWidth;
    }

    public function getBarWidth()
    {
        return $this->barWidth;
    }
    
    public function getBarHeight()
    {
        return $this->barHeight;
    }
}
