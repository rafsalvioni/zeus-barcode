<?php

namespace Zeus\Barcode;

/**
 * Implements a UPC-A barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/upca.phtml
 */
class UpcA extends AbstractBarcode
{
    /**
     * UPC-A is a subset of EAN-13. So...
     * 
     * @var Ean13
     */
    private $ean13;
    
    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        $this->ean13 = new Ean13('0' . $data, $hasChecksum);
        parent::__construct($data, $hasChecksum);
        $this->data  = \substr($this->ean13->getData(true), 1);
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

    /**
     * Return the Ean-13 checksum.
     * 
     * @param string $data
     * @return string
     */
    protected function calcChecksum($data)
    {
        return $this->ean13->getChecksum();
    }

    /**
     * Always return true. Not used...
     *  
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        return true;
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        return $this->ean13->getEncoded();
    }
}

/**
 * Default class exception
 * 
 */
class UpcAException extends Exception {}

