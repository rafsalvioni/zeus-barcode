<?php

namespace Zeus\Barcode\Renderer;

/**
 * Implements a simple barcode HTML renderer.
 *
 * @author Rafael M. Salvioni
 */
class HtmlRenderer implements RendererInterface
{
    use RendererTrait;
    
    /**
     * HTML resource
     * 
     * @var string[]
     */
    private $html = [];
    /**
     * Printable text resource
     * 
     * @var string
     */
    private $text;
    /**
     * Width of barcode
     * 
     * @var int
     */
    private $width;

    /**
     * 
     * @param bool $bar
     * @param number $width
     * @param number $height
     * @param number $posy
     * @return self
     */
    public function drawBar($bar, $width = 1, $height = 1, $posy = 0)
    {
        $this->html[] = \sprintf(
            '<div style="width:%dpx;height:%dpx;margin-top:%dpx;background-color:%s;float:left"></div>',
            $width * $this->barWidth,
            $height * $this->barHeight,
            $posy * $this->barHeight,
            $bar ? '#000' : '#fff'
        );
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getResource()
    {
        $res = \implode("\n", $this->html);
        if ($this->text) {
            $res .= "\n{$this->text}";
        }
        $res .= "\n</div>\n";
        return $res;
    }

    /**
     * 
     */
    public function show()
    {
        \header('Content-Type: text/html');
        echo $this->getResource();
    }

    /**
     * 
     * @param number $width
     * @param number $height
     * @return self
     */
    public function startResource($width, $height = 1)
    {
        $width       = $this->barWidth * $width;
        $height      = $this->barHeight * $height;
        $this->html  = [\sprintf('<div style="width:%dpx;height:%dpx;display:block">', $width, $height)];
        $this->width = $width;
        return $this;
    }

    /**
     * 
     * @param string $text
     * @param number $size
     * @param number $posy
     */
    public function writeText($text, $size = 1, $posy = 0)
    {
        $posy = $this->barHeight * $posy;
        $this->text = \sprintf(
            '<div style="position:relative;width:100%%;height:%dpx;margin-top:%dpx;clear:both;font-size:%dpx;display:block;text-align:center">%s</div>',
            $size, $posy, $size, $text
        );
    }
}
