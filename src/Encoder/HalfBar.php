<?php

namespace Zeus\Barcode\Encoder;

/**
 * Encodes barcodes using the height of bars.
 * 
 * Using 1 to represent full bars and 0 for half bars. Spaces between bars
 * are inserted automatically.
 *
 * @author Rafael M. Salvioni
 */
class HalfBar extends AbstractEncoder
{
    /**
     * Add a full bar.
     * 
     * @param int $width
     * @return self
     */
    public function addFull($width = 1)
    {
        for ($i = 0; $i < $width; $i++) {
            $this->append('1');
            $this->append('0', 1, 1, 0, false);
        }
        return $this;
    }
    
    /**
     * Add a half bar
     * 
     * @param int $width
     * @return self
     */
    public function addHalf($width = 1)
    {
        for ($i = 0; $i < $width; $i++) {
            $this->append('1', 1, 0.5, 0.5, false);
            $this->append('0');
        }
        return $this;
    }
    
    /**
     * 
     * @return HalfBar
     */
    public function close()
    {
        parent::close();
        if (!empty($this->bars)) {
            unset($this->bars[$this->last]);
            $this->last--;
        }
        return $this;
    }

    /**
     * 
     * @param string $bin
     * @param int $width
     * @param number $height
     * @return HalfBar
     */
    protected function processBinary($bin, $width, $height)
    {
        if ($bin == '1') {
            $this->addFull($width);
        }
        else {
            $this->addHalf($width);
        }
        return $this;
    }
}
