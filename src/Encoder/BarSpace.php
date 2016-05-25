<?php

namespace Zeus\Barcode\Encoder;

/**
 * Encodes barcodes in bars and spaces.
 * 
 * A bar is represented by 1 and spaces by 0.
 *
 * @author Rafael M. Salvioni
 */
class BarSpace extends AbstractEncoder
{
    /**
     * Adds a bar.
     * 
     * @param int $width
     * @param number $height
     * @return self
     */
    public function addBar($width = 1, $height = 1)
    {
        return $this->append('1', (int)$width, $height, true);
    }
    
    /**
     * Adds a space.
     * 
     * @param int $width
     * @return self
     */
    public function addSpace($width = 1)
    {
        return $this->append('0', (int)$width, 1, true);
    }

    /**
     * 
     * @param string $bin
     * @param int $width
     * @param number $height
     * @return BarSpace
     */
    protected function processBinary($bin, $width, $height)
    {
        if ($bin == '1') {
            $this->addBar($width, $height);
        }
        else {
            $this->addSpace($width);
        }
        return $this;
    }
}
