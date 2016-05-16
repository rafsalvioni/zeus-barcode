<?php

namespace Zeus\Barcode;

/**
 * Identifies a barcode that have a checksum embedded in its data.
 * 
 * @author Rafael M. Salvioni
 */
interface ChecksumBarcodeInterface extends BarcodeInterface
{
    /**
     * Constructor should be accepts a barcode data and, if a data is invalid,
     * a exception will should throw.
     * 
     * If $hasChecksum was given, the checksum digit of data should be checked
     * too. Otherwise, the class will should calculate the checksum.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @throws Exception
     */
    public function __construct($data, $hasChecksum = true);

    /**
     * Returns the barcode data without checksum.
     * 
     * @return string
     */
    public function getDataWithoutChecksum();

    /**
     * Returns the barcode checksum.
     * 
     * @return string
     */
    public function getChecksum();
}
