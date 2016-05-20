<?php

namespace Zeus\Barcode\Encoder;

/**
 *
 * @author rafaelsalvioni
 */
interface EncoderInterface extends \Iterator, \Countable
{
    public function addBinary($bin);
    
    public function getBinary();
    
    public function getHeightFactor();
    
    public function getWidthFactor();
    
    public function isClosed();
    
    public function close();
}
