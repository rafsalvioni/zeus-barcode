<?php

namespace Zeus\Barcode\Encoder;

/**
 * Identifies a barcode encoder.
 * 
 * Barcodes can be encoded by many ways. By widths, by heights etc. So, 
 * we can create encoders amd attach one on our barcodes.
 * 
 * Encoders receives binary data and encode them to export in bars to be
 * draw.
 * 
 * @author Rafael M. Salvioni
 */
interface EncoderInterface extends \Iterator, \Countable
{
    /**
     * Adds a binary string to encode.
     * 
     * @param string $bin
     * @return self
     */
    public function addBinary($bin);
    
    /**
     * Returns the binary representation from encoded bars.
     * 
     * @return string
     */
    public function getBinary();
    
    /**
     * Returns the width factor from this bar set.
     * 
     * @return number
     */
    public function getWidthFactor();
    
    /**
     * Checks if encoder is closed.
     * 
     * @see close() method
     * @return bool
     */
    public function isClosed();
    
    /**
     * Close the encoder.
     * 
     * A closed encoder will be ignore additional binary data to be appended.
     * 
     * @return self
     */
    public function close();
}
