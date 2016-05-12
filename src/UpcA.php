<?php

namespace Zeus\Barcode;

/**
 * Implements a UPC-A barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/upca.phtml
 */
class UpcA extends Ean13
{
    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        parent::__construct('0' . $data, $hasChecksum);
    }
    
    /**
     * 
     * @param bool $withChecksum
     * @return string
     */
    public function getData($withChecksum = false)
    {
        $data = parent::getData($withChecksum);
        return \substr($data, 1);
    }
    
    /**
     * Separates using a space the system number.
     * 
     * @return string
     */
    public function getPrintableData()
    {
        $data = parent::getPrintableData();
        $data = \substr($data, 0, -1) . ' ' . \substr($data, -1);
        return $data;
    }
}
