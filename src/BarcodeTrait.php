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
        return (int)\ceil(
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
        return (int)\ceil(
                    $this->options['barheight'] *
                    $this->getEncoded()->getHeightFactor()
                 );
    }
    
    /**
     * 
     * @return int
     */
    public function getTotalWidth()
    {
        $width = ($this->options['border'] * 2) +
                 ($this->options['quietzone'] * 2) +
                 $this->getWidth();
        
        return (int)\ceil($width);
    }
    
    /**
     * 
     * @return int
     */
    public function getTotalHeight()
    {
        $height = ($this->options['border'] * 2) +
                   $this->getHeight();
        
        if ($this->options['showtext']) {
            $height += $this->getTextHeight();
        }
        
        return (int)\ceil($height);
    }
    
    /**
     * Calcs the text height using draw options.
     * 
     * @return number
     */
    protected function getTextHeight()
    {
        return \max($this->options['fontsize'] + 3, 15);
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
        $this->drawBorder($renderer);
        $this->drawBarcode($renderer);
        
        if ($this->options['showtext']) {
            $this->drawText($renderer);
        }
    }

    /**
     * Draws the barcode border.
     * 
     * @param Renderer\RendererInterface $renderer
     */
    protected function drawBorder(Renderer\RendererInterface &$renderer)
    {
        // Draws the border
        $width  = $this->getTotalWidth() - 1;
        $height = $this->getTotalHeight() - 1;
        $border = $this->border;
        
        for ($i = 0; $i < $border; $i++) {
            $renderer->drawRect([
                [0 + $i, 0 + $i],
                [$width - $i, $i],
                [$width - $i, $height - $i],
                [$i, $height - $i],
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
        $barOffsetX = $this->border + $this->quietZone;
        $barOffsetY = $this->border;
        $barX       = $barOffsetX;
        $barY       = $barOffsetY;
        
        if ($this->showText && $this->textPosition == 'top') {
            $barOffsetY += $this->getTextHeight();
        }
        
        foreach ($this->getEncoded() as $bar) {
            $barWidth  = ($bar->w * $this->options['barwidth']) - 1;
            $barHeight = ($bar->h * $this->options['barheight']) - 1;
            $barY      = $barOffsetY + ($bar->y * $this->options['barheight']);
            if ($bar->b) {
                $renderer->drawRect([
                    [$barX, $barY], [$barX + $barWidth, $barY],
                    [$barX + $barWidth, $barY + $barHeight], [$barX, $barY + $barHeight],
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
        $text   = $this->getDataToDisplay();
        $offset = $this->options['border'] + $this->options['quietzone'];
        switch ($this->options['textposition']) {
            case 'top':
                $y = $this->options['border'] + 1;
                break;
            default:
                $y = $this->options['border'] + $this->getHeight() + 2;
        }
        switch ($this->options['textalign']) {
            case 'center':
                $x = $this->getTotalWidth() / 2;
                break;
            case 'right':
                $x = $this->getTotalWidth() - $offset;
                break;
            default:
                $x = $offset;
                break;
        }
        $renderer->drawText(
            [$x, $y],
            $text,
            $this->options['forecolor'],
            $this->options['font'],
            $this->options['fontsize'],
            $this->options['textalign']
        );
    }
}
