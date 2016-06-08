<?php

namespace Zeus\Barcode\Renderer;

use Zeus\Barcode\BarcodeInterface;
use Zeus\Barcode\OptionsTrait;

/**
 * Trait to implement default methods and features of RendererInterface.
 *
 * @author Rafael M. Salvioni
 */
trait RendererTrait
{
    use OptionsTrait;
    
    /**
     * Units conversors
     * 
     * @var array
     */
    protected static $units = [
        'pt' => 1,
        'px' => 0.75,
        'mm' => 72/25.4,
        'cm' => 72/2.54,
        'in' => 72,
    ];
    
    /**
     * Draw resource
     * 
     * @var mixed
     */
    protected $resource;
    /**
     * Barcode
     * 
     * @var BarcodeInterface
     */
    protected $barcode;

    /**
     * Converts a integer to a RGB color.
     * 
     * Returns a array [R, G, B].
     * 
     * @param int $color
     * @return int[]
     */
    protected static function colorToRgb($color)
    {
        return [
            ($color & 0xff0000) >> 16,
            ($color & 0x00ff00) >> 8,
            ($color & 0x0000ff)
        ];
    }
    
    /**
     * Convert values between units.
     * 
     * @param number $value
     * @param string $from From unit
     * @param string $to To unit
     * @return number
     */
    protected static function convertToUnit($value, $from, $to)
    {
        $from  = \array_get(self::$units, $from, 0);
        $to    = \array_get(self::$units, $to, 0);
        $value = ($value / $to) * $from;
        return $value;
    }

    /**
     * 
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * 
     * @param BarcodeInterface $barcode
     * @return self
     */
    public function start(BarcodeInterface $barcode)
    {
        if ($this->barcode !== $barcode || $this->options['merge']) {
            $this->barcode = $barcode;
            $this->initResource();
        }
        return $this;
    }
    
    /**
     * 
     * @param BarcodeInterface $barcode
     * @return self
     */
    public function stream(BarcodeInterface $barcode)
    {
        $merged = $this->options['merge'];
        $this->options['merge'] = true;
        $barcode->draw($this);
        $this->options['offsetleft'] += $barcode->getTotalWidth();
        $this->options['merge'] = $merged;
        return $this;
    }

    /**
     * Initializes the barcode resource.
     * 
     * Should be fill barcode area with background color.
     * 
     */
    abstract protected function initResource();
    
    /**
     * Helper function to check if renderer is started.
     * 
     * Throws a exception if not.
     * 
     * @throws Exception
     */
    protected function checkStarted()
    {
        if (!$this->resource) {
            throw new Exception('Renderer is not started!');
        }
    }
    
    /**
     * Loads the default renderer options.
     * 
     */
    protected function loadDefaultOptions()
    {
        $this->options = [
            'offsettop'  => 0,
            'offsetleft' => 0,
            'merge'      => false,
            'backcolor'  => 0xffffff,
        ];
    }
}
