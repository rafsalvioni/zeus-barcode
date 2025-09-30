<?php

namespace Zeus\Barcode\Encoder;

/**
 * Trait to implement default methods of EncoderInterface.
 *
 * @author Rafael M. Salvioni
 */
trait EncoderTrait
{
    /**
     * Registered bars
     * 
     * @var array
     */
    protected $bars   = [];
    /**
     * Binary data
     * 
     * @var string
     */
    protected $binary = '';
    /**
     * Width factor
     * 
     * @var number
     */
    protected $width  = 0;
    /**
     * Height factor
     * 
     * @var number
     */
    protected $height = 0;
    /**
     * Index of last bar added
     * 
     * @var int
     */
    protected $last   = -1;
    /**
     * Is closed?
     * 
     * @var bool
     */
    protected $closed = false;

    /**
     * 
     * @param string $bin
     * @param number $height
     * @return self
     */
    public function addBinary($bin, $height = 1)
    {
        while (\preg_match('/^(0+|1+)/', $bin, $match)) {
            $width = \strlen($match[1]);
            $bin   = \substr($bin, $width);
            $this->processBinary($match[1][0], $width, $height);
        }
        return $this;
    }

    /**
     * 
     * @return int
     */
    public function count()
    {
        return \count($this->bars);
    }

    /**
     * 
     * @return \stdClass
     */
    public function current()
    {
        $cur = \current($this->bars);
        return (object)$cur;
    }

    /**
     * 
     * @return string
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * 
     * @return number
     */
    public function getHeightFactor()
    {
        return $this->height;
    }

    /**
     * 
     * @return number
     */
    public function getWidthFactor()
    {
        return $this->width;
    }

    /**
     * 
     * @return self
     */
    public function close()
    {
        $this->closed = true;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * 
     * @return int
     */
    public function key()
    {
        return \key($this->bars);
    }

    /**
     * 
     */
    public function next()
    {
        \next($this->bars);
    }

    /**
     * 
     */
    public function rewind()
    {
        \reset($this->bars);
    }

    /**
     * 
     * @return bool
     */
    public function valid()
    {
        return $this->key() !== null;
    }
    
    /**
     * 
     * @param string $bin 0 or 1
     * @param number $width
     * @param number $height
     * @param number $posY
     * @param bool $isData Sets if the bin is part of barcode data
     * @return self
     */
    protected function append($bin, $width = 1, $height = 1, $posY = 0, $isData = true)
    {
        if ($this->closed || $width <= 0 || $height <= 0) {
            return $this;
        }
        
        $append = true;
        $bar    = [
            'b' => $bin == '1',
            'w' => (int)$width,
            'h' => (float)$height,
            'y' => (float)$posY
        ];
        
        if ($this->last >= 0) {
            $last =& $this->bars[$this->last];
            if ($bar['b'] === $last['b'] && $bar['h'] == $last['h'] && $bar['y'] == $last['y']) {
                $last['w'] += $bar['w'];
                $append = false;
            }
        }
        
        if ($append) {
            $this->bars[] = $bar;
            $this->height = \max($this->height, $bar['h'] + $bar['y']);
            $this->last++;
        }
        $this->width += $bar['w'];
        
        if ($isData) {
            $this->binary .= \str_repeat($bin, $width);
        }
        
        return $this;
    }
    
    /**
     * Will be receives the binary data processed by addBinary() method.
     * 
     * @param string $bin 0 or 1
     * @param number $width Length of 0 or 1 finded
     * @param number $height Given barcode
     * @return self
     */
    abstract protected function processBinary($bin, $width, $height);
}
