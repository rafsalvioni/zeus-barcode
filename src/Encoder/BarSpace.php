<?php

namespace Zeus\Barcode\Encoder;

/**
 * Description of BarSpace
 *
 * @author rafaelsalvioni
 */
class BarSpace extends AbstractEncoder
{
    public function addBar($width = 1, $height = 1)
    {
        return $this->append('1', $width, $height, true);
    }
    
    public function addSpace($width = 1)
    {
        return $this->append('0', $width, 1, true);
    }

    protected function processBinary($bin, $width)
    {
        if ($bin == '1') {
            $this->addBar($width);
        }
        else {
            $this->addSpace($width);
        }
        return $this;
    }
}
