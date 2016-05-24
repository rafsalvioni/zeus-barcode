<?php

namespace Zeus\Barcode\Upc;

use Zeus\Barcode\AbstractChecksumBarcode;
use Zeus\Barcode\FixedLengthInterface;
use Zeus\Barcode\Encoder\EncoderInterface;

/**
 * Implements a UPC-A barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/upca.phtml
 */
class Upca extends AbstractChecksumBarcode implements FixedLengthInterface
{
    /**
     * Manufacturer field
     * 
     */
    const MANUFACTURER = 1;
    /**
     * Product field
     * 
     */
    const PRODUCT      = 6;
    
    /**
     * UPC-A is a subset of EAN-13. So...
     * 
     * @var Ean13
     */
    private $ean13;
    
    /**
     * Builder to create a UPC-A instance.
     * 
     * @param string $mfc Manufacturer code. 1-5 digits
     * @param string $prod Product code. 1-5 digits
     * @return Upca
     */
    public static function builder($mfc, $prod)
    {
        $data = \str_repeat('0', 11);
        $me   = new self($data, false);
        return $me->withManufacturerCode($mfc)->withProductCode($prod);
    }

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        $this->ean13 = new Ean13('0' . $data, $hasChecksum);
        parent::__construct($data, $hasChecksum);
        $this->data  = \substr($this->ean13->getData(), 1);
    }
    
    /**
     * Returns 12.
     * 
     * @return int
     */
    public function getLength()
    {
        return 12;
    }

    /**
     * Checks if this barcode is compatible to UPC-E.
     * 
     * @return bool
     */
    public function isUpceCompatible()
    {
        $data = $this->getRealData();
        return \preg_match('/([0-2]0{4}[0-9]{3}|0{5}[0-9]{2}|0{5}[0-9]|0{4}[5-9])$/', $data);
    }
    
    /**
     * Converts this barcode to a UPC-E barcode.
     * 
     * @return Upce
     * @throws UpcaException If was unconvertable
     */
    public function toUpce()
    {
        $data    = $this->getRealData();
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
            throw new UpcaException('Uncompatible UPC-A barcode!');
        }
        
        $data = $system . $data;
        return new Upce($data, false);
    }
    
    /**
     * 
     * @return string
     */
    public function getPrintableData()
    {
        $data = parent::getPrintableData();
        $data = \substr_replace($data, ' ', 1, 0);
        $data = \substr_replace($data, ' ', -1, 0);
        return $data;
    }
    
    /**
     * Returns the manufacturer code.
     * 
     * @return string
     */
    public function getManufacturerCode()
    {
        return $this->getDataPart(self::MANUFACTURER, 5);
    }
    
    /**
     * Create a new instance with another manufacturer code.
     * 
     * @param string $code Code, 1-5 digits
     * @return Upca
     */
    public function withManufacturerCode($code)
    {
        $data = $this->withDataPart($code, self::MANUFACTURER, 5);
        return new self($data, false);
    }

    /**
     * Returns the product code.
     * 
     * @return string
     */
    public function getProductCode()
    {
        return $this->getDataPart(self::PRODUCT, 5);
    }
    
    /**
     * Create a new instance with another product code.
     * 
     * @param string $code Code, 1-5 digits
     * @return Upca
     */
    public function withProductCode($code)
    {
        $data = $this->withDataPart($code, self::PRODUCT, 5);
        return new self($data, false);
    }
    
    /**
     * Returns a Ean13 barcode representation of current barcode.
     * 
     * @return Ean13
     */
    public function toEan13()
    {
        return clone $this->ean13;
    }

    /**
     * Return the Ean-13 checksum.
     * 
     * @param string $data
     * @return string
     */
    protected function calcChecksum($data)
    {
        return (new Ean13('0' . $data, false))->getChecksum();
    }

    /**
     * Always return true. Not used...
     *  
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return true;
    }

    /**
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    protected function encodeData(EncoderInterface &$encoder, $data)
    {
        $encoder = $this->ean13->getEncoded();
    }
}

/**
 * Default class exception
 * 
 */
class UpcaException extends Exception {}

