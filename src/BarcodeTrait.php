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
     * Auxiliar function to calculate checksums using crescent weights.
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
     * Auxiliar function to calculate checksums using decrescent weights.
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
        $renderer->start($this);
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
        $width = ($this->getContentOffsetLeft() * 2) + $this->getWidth();
        return (int)\ceil($width);
    }
    
    /**
     * 
     * @return int
     */
    public function getTotalHeight()
    {
        $height = ($this->getContentOffsetTop() * 2) + $this->getHeight();
        if ($this->options['showtext']) {
            $height += $this->getTextHeight();
        }
        return (int)\ceil($height);
    }
    
    /**
     * Return the general offset left.
     * 
     * @return number
     */
    protected function getContentOffsetLeft()
    {
        return $this->options['border'] +
               $this->options['quietzone'];
    }

    /**
     * Return the general offset top.
     * 
     * @return number
     */
    protected function getContentOffsetTop()
    {
        return $this->options['border'];
    }
    
    /**
     * Return the text's X position.
     * 
     * @return number
     */
    protected function getTextX()
    {
        $x = $this->getContentOffsetLeft();
        switch ($this->options['textalign']) {
            case 'center':
                $x += $this->getWidth() / 2;
                break;
            case 'right':
                $x += $this->getWidth();
                break;
        }
        return $x;
    }
    
    /**
     * Return the text's Y position.
     * 
     * @return number
     */
    protected function getTextY()
    {
        switch ($this->options['textposition']) {
            case 'top':
                $y = $this->getContentOffsetTop() + 1;
                break;
            default:
                $y = $this->getContentOffsetTop() + $this->getHeight() + 2;
        }
        return $y;
    }
    
    /**
     * Return the barcode's offset top.
     * 
     * @return number
     */
    protected function getBarcodeOffsetTop()
    {
        $offset = $this->getContentOffsetTop();
        if ($this->options['showtext'] && $this->options['textposition'] == 'top') {
            $offset += $this->getTextHeight();
        }
        return $offset;
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
        $width  = $this->getTotalWidth();
        $height = $this->getTotalHeight();
        $border =& $this->options['border'];
        $color  =& $this->options['forecolor'];
        
        if ($border >= 4) {
            $renderer->drawRect([0, 0], $border, $height, $color, true);
            $renderer->drawRect([0, 0], $width, $border, $color, true);
            $renderer->drawRect([$width - $border, 0], $border, $height, $color, true);
            $renderer->drawRect([0, $height - $border], $width, $border, $color, true);
        }
        else if ($border > 0) {
            for ($i = 0; $i < $border; $i++) {
                $renderer->drawRect(
                    [$i, $i],
                    $width-- - $i,
                    $height-- - $i,
                    $this->options['forecolor'],
                    false
                );
            }
        }
    }
    
    /**
     * Draws the bars.
     * 
     * @param Renderer\RendererInterface $renderer
     */
    protected function drawBarcode(Renderer\RendererInterface &$renderer)
    {
        $barOffsetX = $this->getContentOffsetLeft();
        $barOffsetY = $this->getBarcodeOffsetTop();
        $barX       = $barOffsetX;
        $barY       = $barOffsetY;
        
        foreach ($this->getEncoded() as $bar) {
            $barWidth  = ($bar->w * $this->options['barwidth']);
            $barHeight = ($bar->h * $this->options['barheight']);
            $barY      = $barOffsetY + ($bar->y * $this->options['barheight']);
            if ($bar->b) {
                $renderer->drawRect(
                    [$barX, $barY],
                    $barWidth,
                    $barHeight,
                    $this->options['forecolor'],
                    true
                );
            }
            $barX += $barWidth;
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
        $text = $this->getDataToDisplay();
        $x    = $this->getTextX();
        $y    = $this->getTextY();
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
