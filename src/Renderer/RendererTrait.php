<?php

namespace Zeus\Barcode\Renderer;

/**
 * Description of RendererTrait
 *
 * @author rafaelsalvioni
 */
trait RendererTrait
{
    protected $bars      = [];
    protected $text;
    protected $textAlign = RendererInterface::TEXT_ALIGN_CENTER;
    protected $textPosition;
    protected $barHeight = 60;
    protected $barWidth  = 2;

    public function resetDraw()
    {
        $this->bars = [];
        $this->text = null;
        return $this;
    }

    public function setText($text)
    {
        $this->text = (string)$text;
        return $this;
    }

    public function setTextAlign($align)
    {
        $this->textAlign = (string)$align;
        return $this;
    }

    public function setTextPosition($position)
    {
        $this->textPosition = (string)$position;
        return $this;
    }
    
    public function setBarWidth($width)
    {
        $this->barWidth = (int)$width;
        return $this;
    }
    
    public function setBarHeight($height)
    {
        $this->barHeight = (int)$height;
        return $this;
    }
}
