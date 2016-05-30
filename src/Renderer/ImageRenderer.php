<?php

namespace Zeus\Barcode\Renderer;

use Zeus\Barcode\Encoder\EncoderInterface;

/**
 * Renderer to draw barcodes as images.
 *
 * @author Rafael M. Salvioni
 */
class ImageRenderer implements RendererInterface
{
    use RendererTrait;
    
    /**
     * 
     * @param EncoderInterface $encoder
     * @param array $options
     */
    protected function draw()
    {
        $encoder   = $this->barcode->getEncoded();
        $options   = $this->barcode->getOptions();
        $text      = $this->barcode->getDataToDisplay();
        
        $textWidth = $textHeight = 0;
        if ($options['showtext']) {
            if (empty($options['font'])) {
                $options['font'] = $options['fontsize'] % 6;
                $textWidth       = \imagefontwidth($options['font']);
                $textHeight      = \imagefontheight($options['font']);
            }
            else {
                $options['font'] = \imageloadfont($options['font']);
                $textWidth = $textHeight = $options['fontsize'];
            }
        }
        $textWidth *= \strlen($text);
        
        $width         = $this->calcBarcodeWidth();
        $height        = $this->calcBarcodeHeight() + $textHeight;
        
        $this->resource = \imagecreatetruecolor($width, $height);
        $bgColor  = \imagecolorallocate(
                        $this->resource,
                        ($options['backcolor'] & 0xff0000) >> 16,
                        ($options['backcolor'] & 0x00ff00) >> 8,
                        ($options['backcolor'] & 0x0000ff)
                    );
        $foreColor = \imagecolorallocate(
                        $this->resource,
                        ($options['forecolor'] & 0xff0000) >> 16,
                        ($options['forecolor'] & 0x00ff00) >> 8,
                        ($options['forecolor'] & 0x0000ff)
                     );
        \imagefill($this->resource, 0, 0, $bgColor);
        
        if ($options['border'] > 0) {
            $borderOffset = $options['border'] / 2;
            \imagesetthickness($this->resource, $options['border']);
            \imagerectangle(
                $this->resource,
                $borderOffset,
                $borderOffset,
                $width - $borderOffset,
                $height - $borderOffset,
                $foreColor
            );
            \imagesetthickness($this->resource, 1);
        }

        $barX = $options['border'] + $options['quietzone'];
        $barY = $options['border'];
        if ($options['textposition'] == 'top') {
            $barY += $textHeight;
        }
        
        $x1 = $barX + 1;

        foreach ($encoder as $bar) {
            $x2 = $x1 + ($options['barwidth'] * $bar->w) - 1;
            $y1 = $barY + ($options['barheight'] * $bar->y) - 1;
            $y2 = $y1 + ($options['barheight'] * $bar->h);
            if ($bar->b) {
                \imagefilledrectangle(
                    $this->resource,
                    $x1,
                    $y1,
                    $x2,
                    $y2,
                    $foreColor
                );
            }
            $x1 = $x2 + 1;
        }
        
        if ($options['showtext']) {
            switch ($options['textalign']) {
                case 'center':
                    $x1 = $barX + self::calcCenter($this->barcode->getWidth(), $textWidth) + 2;
                    break;
                case 'right':
                    $x1 = $barX + self::calcRight($this->barcode->getWidth(), $textWidth);
                    break;
                default:
                    $x1 = $barX;
            }
            $x1++;
            if ($options['textposition'] == 'top') {
                $y1 = $barY - $textHeight - 2;
            }
            else {
                $y1 = $barY + $this->barcode->getHeight();
            }
            if (\is_int($options['font'])){
                \imagestring($this->resource, $options['font'], $x1, $y1, $text, $foreColor);
            }
            else {
                \imagettftext($this->resource, $options['fontsize'], 180, $x1, $y1, $foreColor, $options['font'], $text);
            }
        }
    }

    /**
     * Render as PNG
     * 
     */
    public function render()
    {
        \header('Content-Type: image/png');
        \imagepng($this->resource);
    }
}
