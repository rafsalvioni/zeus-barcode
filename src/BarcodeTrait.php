<?php

namespace Zeus\Barcode;

use Zeus\Barcode\Encoder\EncoderInterface;
use Zeus\Barcode\Encoder\BarSpace;

/**
 * Trait to implements some methods of BarcodeInterface.
 *
 * @author Rafael M. Salvioni
 */
trait BarcodeTrait
{
    use OptionsTrait;
    
    /**
     * Stores the barcode data.
     * 
     * @var string
     */
    protected $data;
    /**
     * Stores the encoded object. Its a cache. Use getEncoded().
     * 
     * @var EncoderInterface
     */
    protected $encoded;

    /**
     * Auxiliar function to calculate checksums alterning between weights.
     * 
     * The order is from right to left.
     * 
     * Returns the sum result.
     * 
     * @param array $data Array of integers
     * @param int $firstWeight
     * @param int $secWeight
     * @return int
     */
    protected static function sumAlternateWeight(array $data, $firstWeight, $secWeight)
    {
        $sum    = 0;
        $weight = $firstWeight;
        while (!empty($data)) {
            $sum   += $weight * (int)\array_pop($data);
            $weight = $weight == $firstWeight ? $secWeight : $firstWeight;
        }
        return $sum;
    }
    
    /**
     * Auxiliar function to calculate checksums using cresacent weights.
     * 
     * The order is from right to left.
     * 
     * Returns the sum result.
     * 
     * @param array $data Array of integers
     * @param int $minWeight
     * @param int $maxWeight
     * @return int
     */
    protected static function sumCrescentWeight(array $data, $minWeight, $maxWeight = null)
    {
        $sum    = 0;
        $weight = $minWeight;
        while (!empty($data)) {
            $sum += $weight++ * (int)\array_pop($data);
            if ($maxWeight !== null && $weight > $maxWeight) {
                $weight = $minWeight;
            }
        }
        return $sum;
    }

    /**
     * Auxiliar function to calculate checksums using cresacent weights.
     * 
     * The order is from right to left.
     * 
     * Returns the sum result.
     * 
     * @param array $data Array of integers
     * @param int $maxWeight
     * @param int $minWeight
     * @return int
     */
    protected static function sumDecrescentWeight(array $data, $maxWeight, $minWeight = null)
    {
        $sum    = 0;
        $weight = $maxWeight;
        while (!empty($data)) {
            $sum += $weight-- * (int)\array_pop($data);
            if ($minWeight !== null && $weight < $minWeight) {
                $weight = $maxWeight;
            }
        }
        return $sum;
    }
    
    /**
     * Padding zeros on left.
     * 
     * @param string $data
     * @param int $length
     * @return string
     */
    protected static function zeroLeftPadding($data, $length)
    {
        return \str_pad($data, $length, '0', \STR_PAD_LEFT);
    }
    
    protected static function centerPosition($widthArea, $widthObject)
    {
        return \round(($widthArea - $widthObject) / 2);
    }
    
    protected static function rightPosition($widthArea, $widthObject)
    {
        return \round($widthArea - $widthObject);
    }

    /**
     * 
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * 
     * @return string
     */
    public function getDataToDisplay()
    {
        return $this->getData();
    }
    
    /**
     * 
     * @return EncoderInterface
     */
    public function getEncoded()
    {
        if (empty($this->encoded)) {
            $this->encoded = new BarSpace();
            $this->encodeData($this->encoded, $this->data);
            $this->encoded->close();
        }
        return $this->encoded;
    }
    
    /**
     * 
     * @param int $start
     * @param int $length
     * @return string
     */
    public function getDataPart($start, $length = null)
    {
        return \substr($this->data, $start, $length);
    }
    
    /**
     * 
     * @param string $value
     * @param int $start
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function withDataPart($value, $start, $length)
    {
        $value = self::zeroLeftPadding($value, $length);
        if (\strlen($value) == $length) {
            $data = \substr_replace($this->data, $value, $start, $length);
            return $data;
        }
        throw new Exception('Wrong data part length!');
    }
    
    /**
     * 
     * @param Renderer\RendererInterface $renderer
     * @return Renderer\RendererInterface
     */
    public function draw(Renderer\RendererInterface $renderer)
    {
        $renderer->setBarcode($this);
        $this->drawInstructions($renderer);
        return $renderer;
    }
    
    /**
     * 
     * @return int
     */
    public function getWidth()
    {
        return (int)\round(
                    $this->options['barwidth'] *
                    $this->getEncoded()->getWidthFactor()
                 );
    }
    
    /**
     * 
     * @return int
     */
    public function getHeight()
    {
        return (int)\round(
                    $this->options['barheight'] *
                    $this->getEncoded()->getHeightFactor()
                 );
    }
    
    /**
     * Encodes a data and put them on Encoder given.
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    abstract protected function encodeData(EncoderInterface &$encoder, $data);

    /**
     * Encapsulate draw routines.
     * 
     * @param Renderer\RendererInterface $renderer
     */
    protected function drawInstructions(Renderer\RendererInterface &$renderer)
    {
        $width  = $renderer->getTotalWidth() - 1;
        $height = $renderer->getTotalHeight() - 1;
        
        $this->fillBarcodeArea($renderer, $width, $height);
        $this->drawBorder($renderer, $width, $height);
        $this->drawBarcode($renderer);
        
        if ($this->options['showtext']) {
            $this->drawText($renderer);
        }
    }

    /**
     * Fill barcode area.
     * 
     * @param Renderer\RendererInterface $renderer
     * @param int $width
     * @param int $height
     */
    protected function fillBarcodeArea(Renderer\RendererInterface &$renderer, $width, $height)
    {
        $renderer->drawPolygon([
            ['x' => 0, 'y' => 0],
            ['x' => $width, 'y' => 0],
            ['x' => $width, 'y' => $height],
            ['x' => 0, 'y' => $height],
        ], $this->options['backcolor'], true);
    }

    /**
     * Draws the barcode border.
     * 
     * @param Renderer\RendererInterface $renderer
     * @param int $width
     * @param int $height
     */
    protected function drawBorder(Renderer\RendererInterface &$renderer, $width, $height)
    {
        // Draws the border
        $border = $this->border;
        
        for ($i = 0; $i < $border; $i++) {
            $renderer->drawPolygon([
                ['x' => 0 + $i, 'y' => 0 + $i], ['x' => $width - $i, 'y' => $i],
                ['x' => $width - $i, 'y' => $height - $i], ['x' => $i, 'y' => $height - $i],
            ], $this->options['forecolor'], false);
        }
    }
    
    /**
     * Draws the bars.
     * 
     * @param Renderer\RendererInterface $renderer
     */
    protected function drawBarcode(Renderer\RendererInterface &$renderer)
    {
        $barOffsetX = $this->border + $this->quietZone + 1;
        $barOffsetY = $this->border;
        $barX       = $barOffsetX;
        $barY       = $barOffsetY;
        
        if ($this->showText && $this->textPosition == 'top') {
            $barOffsetY += $renderer->getTextHeight() + 3;
        }
        
        foreach ($this->getEncoded() as $bar) {
            $barWidth  = ($bar->w * $this->options['barwidth']) - 1;
            $barHeight = ($bar->h * $this->options['barheight']) - 1;
            $barY      = $barOffsetY + ($bar->y * $this->options['barheight']);
            if ($bar->b) {
                $renderer->drawPolygon([
                    ['x' => $barX, 'y' => $barY], ['x' => $barX + $barWidth, 'y' => $barY],
                    ['x' => $barX + $barWidth, 'y' => $barY + $barHeight], ['x' => $barX, 'y' => $barY + $barHeight],
                ], $this->options['forecolor'], true);
            }
            $barX += $barWidth + 1;
        }
        unset($barOffsetX, $barX, $barY, $barWidth, $barHeight, $bar);
    }
    
    /**
     * Draws the barcode text.
     * 
     * @param Renderer\RendererInterface $renderer
     */
    protected function drawText(Renderer\RendererInterface &$renderer)
    {
        $totalWidth = $renderer->getTotalWidth();
        $text       = $text = $this->getDataToDisplay();
        $textWidth  = $renderer->getTextWidth($text);
        
        switch ($this->options['textalign']) {
            case 'center':
                $x = self::centerPosition($totalWidth, $textWidth);
                break;
            case 'right':
                $x = self::rightPosition($totalWidth - $this->options['border'], $textWidth);
                break;
            default:
                $x = $this->border;
        }
        switch ($this->options['textposition']) {
            case 'top':
                $y = $this->border + 1;
                break;
            default:
                $y = $this->border + 2 + $this->getHeight();
        }
        
        $renderer->drawText(
            ['x' => $x, 'y' => $y],
            $text,
            $this->options['forecolor'],
            $this->options['font'],
            $this->options['fontsize']
        );
    }
}
