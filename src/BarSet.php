<?php

namespace Zeus\Barcode;

/**
 * Represents a set of encoded bars.
 *
 * @author Rafael M. Salvioni
 */
class BarSet implements \Iterator, \Countable
{
    /**
     * Bars
     * 
     * @var array
     */
    private $bars    = [];
    /**
     * Max bar height
     * 
     * @var number
     */
    private $height  = 0;
    /**
     * Sum of bar's width
     * 
     * @var int
     */
    private $width   = 0;
    /**
     * Index of last bar added
     * 
     * @var int
     */
    private $last    = -1;
    /**
     * Binary representation
     * 
     * @var string
     */
    private $bin     = '';
    /**
     * Is close?
     * 
     * @var bool
     */
    private $closed = false;

    /**
     * Add a bar.
     * 
     * @param number $width
     * @param number $height
     * @return self
     */
    public function addBar($width = 1, $height = 1)
    {
        return $this->add(true, $width, $height);
    }
    
    /**
     * Add a space.
     * 
     * @param number $width
     * @return self
     */
    public function addSpace($width = 1)
    {
        return $this->add(false, $width, 1);
    }
    
    /**
     * Add a set of bars and spaces using the string given.
     * 
     * The string should have only 0's and 1's.
     * 
     * In this case, is not possible to define bar height.
     * 
     * @param string $bin
     * @return self
     */
    public function addBinary($bin)
    {
        while (\preg_match('/^(0+|1+)/', $bin, $match)) {
            $width = \strlen($match[1]);
            $bin   = \substr($bin, $width);
            $this->add($match[1]{0} == '1', $width);
        }
        return $this;
    }
    
    /**
     * Returns the height factor for this bar set.
     * 
     * @return number
     */
    public function getHeightFactor()
    {
        return $this->height;
    }
    
    /**
     * Returns the width factor for this bar set.
     * 
     * @return number
     */
    public function getWidthFactor()
    {
        return $this->width;
    }
    
    /**
     * Returns the binary representation of this barcode.
     * 
     * @return string
     */
    public function getBinary()
    {
        return $this->bin;
    }
    
    /**
     * Checks if bar set is closed.
     * 
     * @see close()
     * @return bool
     */
    public function isClosed()
    {
        return $this->closed;
    }
    
    /**
     * Close the bar set to add more bars/spaces.
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
        $bar = \current($this->bars);
        return (object)$bar;
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
        return \key($this->bars) !== null;
    }
    
    /**
     * Add a bar or space to bar set.
     * 
     * @param bool $isBar
     * @param number $width
     * @param number $height
     * @return self
     */
    private function add($isBar, $width = 1, $height = 1)
    {
        if ($this->closed || $width <= 0 || $height <= 0) {
            return $this;
        }
        
        $bar   = ['b' => (bool)$isBar, 'w' => 0, 'h' => $height];
        $add   = true;
        
        if ($this->last >= 0) {
            $last =& $this->bars[$this->last];
            if ($last['b'] && $isBar && $last['h'] == $height) {
                $bar =& $last;
                $add = false;
            }
        }
        
        $bar['w']    += $width;
        $this->width += $width;
        $this->bin   .= \str_repeat($bar['b'] && $bar['h'] >= 1 ? '1' : '0', $width);
        
        if ($add) {
            $this->bars[] = $bar;
            $this->last++;
            $this->height = \max($this->height, $bar['h']);
        }
        
        return $this;
    }
}
