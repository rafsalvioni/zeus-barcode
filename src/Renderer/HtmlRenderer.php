<?php

namespace Zeus\Barcode\Renderer;

/**
 * Description of HtmlRenderer
 *
 * @author rafaelsalvioni
 */
class HtmlRenderer implements RendererInterface
{
    use RendererTrait;
    
    public function drawBar($black, $width = 1, $height = 1)
    {
        $this->bars[] = \sprintf(
            '<div style="width:%dpx; height:%dpx; background-color:%s; float:left"></div>',
            $width * $this->barWidth,
            $height * $this->barHeight,
            $black ? '#000' : '#FFF'
        );
    }

    public function getResource()
    {
        $resource  = \sprintf(
            '<div style="height:auto;display:block">%s</div>',
            \implode("\n", $this->bars)
        );
        
        $textLine = \sprintf(
            '<p style="width:500px;text-align:%s">%s</p>',
            $this->textAlign,
            $this->text
        );
        
        if ($this->text && $this->textPosition) {
            if ($this->textPosition == RendererInterface::TEXT_POSITION_TOP) {
                $resource = $textLine . $resource;
            }
            else if ($this->textPosition == RendererInterface::TEXT_POSITION_BOTTOM) {
                $resource .= $textLine;
            }
        }
        
        return $resource;
    }
}
