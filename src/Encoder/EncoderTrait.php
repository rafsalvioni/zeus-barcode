<?php

namespace Zeus\Barcode\Encoder;

/**
 * Description of EncoderTrait
 *
 * @author rafaelsalvioni
 */
trait EncoderTrait
{
    protected $bars   = [];
    protected $binary = '';
    protected $width  = 0;
    protected $height = 0;
    protected $last   = -1;
    protected $closed = false;

    public function addBinary($bin)
    {
        while (\preg_match('/^(0+|1+)/', $bin, $match)) {
            $width = \strlen($match[1]);
            $bin   = \substr($bin, $width);
            $this->processBinary($match[1]{0}, $width);
        }
        return $this;
    }

    public function count()
    {
        return \count($this->bars);
    }

    public function current()
    {
        $cur = \current($this->bars);
        return (object)$cur;
    }

    public function getBinary()
    {
        return $this->binary;
    }

    public function getHeightFactor()
    {
        return $this->height;
    }

    public function getWidthFactor()
    {
        return $this->width;
    }

    public function close()
    {
        $this->closed = true;
        return $this;
    }

    public function isClosed()
    {
        return $this->closed;
    }

    public function key()
    {
        return \key($this->bars);
    }

    public function next()
    {
        \next($this->bars);
    }

    public function rewind()
    {
        \reset($this->bars);
    }

    public function valid()
    {
        return $this->key() !== null;
    }
    
    protected function append($bin, $width, $height, $registerBin = true)
    {
        if ($this->closed || $width <= 0 || $height <= 0) {
            return $this;
        }
        
        $append = true;
        $bar    = [
            'b' => $bin == '1',
            'w' => $width,
            'h' => $height
        ];
        
        if ($this->last >= 0) {
            $last =& $this->bars[$this->last];
            if ($bar['b'] && $last['b'] && $bar['h'] == $last['h']) {
                $last['w'] += $bar['w'];
                $append = false;
            }
        }
        
        if ($append) {
            $this->bars[] = $bar;
            $this->height = \max($this->height, $bar['h']);
            $this->last++;
        }
        $this->width += $bar['w'];
        
        if ($registerBin) {
            $this->binary .= \str_repeat($bin, $width);
        }
        
        return $this;
    }
    
    abstract protected function processBinary($bin, $width);
}
