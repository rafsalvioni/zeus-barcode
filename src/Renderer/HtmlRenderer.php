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
            '<td style="width: %dpx; height: %dpx; background-color: %s"></td>',
            $width * $this->barWidth,
            $height * $this->barHeight,
            $black ? '#000' : '#FFF'
        );
    }

    public function getResource()
    {
        $tableOpen = '<table cellspacing="0" cellpadding="0" border="0">';
        
        $resource  = \sprintf(
            '<tr height="%d">%s</tr>',
            $this->barHeight,
            \implode("\n", $this->bars)
        );
        
        $textLine = \sprintf(
            '<tr><td colspan="%d" align="%s">%s</td></tr>',
            \count($this->bars),
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
        
        $resource = $tableOpen . $resource . '</table>';
        
        return $resource;
    }
}
