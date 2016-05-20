<?php

namespace Zeus\Barcode\Encoder;

/**
 * Description of BarSpace
 *
 * @author rafaelsalvioni
 */
class HalfBar extends AbstractEncoder
{
    public function addFull($width = 1)
    {
        $this->append('1', $width, 1, true);
        return $this->append('0', 1, 1, false);
    }
    
    public function addHalf($width = 1)
    {
        $this->append('1', $width, 0.5, false);
        return $this->append('0', $width, 1, true);
    }

    protected function processBinary($bin, $width)
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
