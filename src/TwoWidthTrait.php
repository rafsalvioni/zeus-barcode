<?php

namespace Zeus\Barcode;

/**
 * Trait to implement default methods of TwoWidthInterface.
 * 
 * @author Rafael M. Salvioni
 */
trait TwoWidthTrait
{
    /**
     * Wide width
     * 
     * @var int
     */
    protected $wideWidth   = 3;
    /**
     * Narrow width
     * 
     * @var int
     */
    protected $narrowWidth = 1;
    
    /**
     * 
     * @param int $width
     * @return self
     * @throws \LogicException
     */
    public function setNarrowWidth($width)
    {
        if ($width > 0 && $width < $this->wideWidth) {
            $this->narrowWidth = (int)$width;
            $this->encoded     = null;
            return $this;
        }
        throw new \LogicException('Narrow width should be greater than zero and less than wide width');
    }

    /**
     * 
     * @param int $width
     * @return self
     * @throws \LogicException
     */
    public function setWideWidth($width)
    {
        if ($width > 0 && $width > $this->narrowWidth) {
            $this->wideWidth = (int)$width;
            $this->encoded   = null;
            return $this;
        }
        throw new \LogicException('Wide width should be greater than zero and narrow width');
    }

    /**
     * Encodes a bar or space using the current widths.
     * 
     * Returns a string of zeros or ones.
     * 
     * @param bool $narrow Is narrow?
     * @param bool $bar Is bar?
     * @return string
     */
    protected function encodeWithWidth($narrow, $bar)
    {
        return \str_repeat(
            $bar ? '1' : '0',
            $narrow ? $this->narrowWidth : $this->wideWidth
        );
    }
    
    /**
     * Encodes a width char (ex. NWNWW) to a binary string using
     * the defined wide and narrow width.
     * 
     * Encode each char alternating bar and spaces, begin with bar from left
     * to right.
     * 
     * Assuming a "N" as a narrow bar/space and "W" as a wide bar/space.
     * 
     * @param string $nwString narrow/wide encode string
     * @return string
     */
    protected function widthToBinary($nwString)
    {
        $encoded = '';
        $bar     = true;
        
        while (!empty($nwString)) {
            $nw       = \substr_remove($nwString, 0, 1);
            $encoded .= $this->encodeWithWidth($nw == 'N', $bar);
            $bar      = !$bar;
        }
        
        return $encoded;
    }
}
