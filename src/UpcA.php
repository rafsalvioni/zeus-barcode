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
    
    /**
     * Checks if this barcode can be converted to UPC-E.
     * 
     * @return bool
     */
    public function isUpcEConvertable()
    {
        $data = $this->getData(false);
        return \preg_match('/([0-2]0{4}[0-9]{3}|0{5}[0-9]{2}|0{5}[0-9]|0{4}[5-9])$/', $data);
    }

    /**
     * Converts this barcode to a UPC-E barcode.
     * 
     * @return UpcE
     * @throws UpcAException If was unconvertable
     */
    public function toUpcE()
    {
        $data    = $this->getData(false);
        $system  = $data{0};
        $mfct    = \substr($data, 1, 5);
        $product = \substr($data, 6, 5);
        
        if (\preg_match('/[0-2]00$/', $mfct) && \preg_match('/00[0-9]{3}$/', $product)) {
            $data = \substr($mfct, 0, 2) . \substr($product, -3) . $mfct{2};
        }
        else if (\preg_match('/00$/', $mfct) && \preg_match('/000[0-9]{2}$/', $product)) {
            $data = \substr($mfct, 0, 3) . \substr($product, -2) . '3';
        }
        else if (\preg_match('/0$/', $mfct) && \preg_match('/0000[0-9]$/', $product)) {
            $data = \substr($mfct, 0, 4) . \substr($product, -1) . '4';
        }
        else if (\preg_match('/0000[5-9]$/', $product)) {
            $data = $mfct . \substr($product, -1);
        }
        else {
            throw new UpcAException('Unconvertable UPC-A barcode!');
        }
        
        $data = $system . $data;
        return new UpcE($data, false);
    }
}

/**
 * Default class exception
 * 
 */
class UpcAException extends Exception {}

