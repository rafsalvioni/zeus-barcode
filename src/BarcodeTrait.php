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
     * Options to draw barcode
     * 
     * @var array
     */
    protected $drawOptions = [
        'barwidth'     => 1,
        'barheight'    => 50,
        'forecolor'    => 0x000,
        'backcolor'    => 0xffffff,
        'border'       => 0,
        'showtext'     => true,
        'textalign'    => 'center',
        'textposition' => 'bottom',
        'font'         => '',
        'fontsize'     => 3,
        'quietzone'    => 30,
        'offsettop'    => 0,
        'offsetleft'   => 0,
        'offsetright'  => 0,
        'offsetbottom' => 0,
    ];

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
        return $renderer;
    }

    /**
     * 
     * @param string $option
     * @return mixed
     */
    public function getOption($option)
    {
        $option = \strtolower($option);
        return isset($this->drawOptions[$option]) ?
               $this->drawOptions[$option] :
               null;
    }

    /**
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->drawOptions;
    }

    /**
     * 
     * @param string $option
     * @param mixed $value
     * @return self
     * @throws Exception
     */
    public function setOption($option, $value)
    {
        $option = \strtolower($option);
        if (\array_key_exists($option, $this->drawOptions)) {
            $type = \gettype($this->drawOptions[$option]);
            if (\gettype($value) == $type) {
                $this->drawOptions[$option] = $value;
                return $this;
            }
            throw new Exception("Option \"$option\" should be a $type value");
        }
        throw new Exception("Unknown option \"$option\"");
    }
    
    /**
     * 
     * @return number
     */
    public function getWidth()
    {
        return round(
               $this->drawOptions['barwidth'] *
               $this->getEncoded()->getWidthFactor()
            );
    }
    
    /**
     * Returns the height of barcode area.
     * 
     * @return number
     */
    public function getHeight()
    {
        return round(
               $this->drawOptions['barheight'] *
               $this->getEncoded()->getHeightFactor()
            );
    }
    
    /**
     * Allows set draw options using a object property notation.
     * 
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $method = "set$name";
        if (\method_exists($this, $method)) {
            $this->$method($value);
        }
        else {
            $this->setOption($name, $value);
        }
    }
    
    /**
     * Gets draw options as object property.
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getOption($name);
    }
    
    /**
     * Checks if a draw options is set using object property notation.
     * 
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $name = \strtolower($name);
        return isset($this->drawOptions[$name]);
    }
    
    /**
     * Encodes a data and put them on Encoder given.
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    abstract protected function encodeData(EncoderInterface &$encoder, $data);
}
