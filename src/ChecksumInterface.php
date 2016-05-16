<?php

namespace Zeus\Barcode;

/**
 * Identifies a barcode that using a checksum digit embedded on its data.
 * 
 * @author Rafael M. Salvioni
 */
interface ChecksumInterface extends BarcodeInterface
{
    /**
     * Checks if $data is compatible with barcode specification.
     * 
     * If $hasChecksum was true, the checksum digit from data will be checked
     * too. Else, the checksum will be calculated and inserted on $data.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true);

    /**
     * Return the barcode checksum.
     * 
     * @return int
     */
    public function getChecksum();
    
    /**
     * Returns the barcode data without checksum digit(s).
     * 
     * @return string
     */
    public function getRawData();
}
