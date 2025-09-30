<?php

namespace Zeus\Barcode;

/**
 * Implementation of ITF-14 barcode standard.
 * 
 * ITF-14 is a Interleaved 2 of 5 barcode with 14 fixed digits length.
 * 
 * A ITF-14 barcode data should begin with a digit between 0 and 8 and it is
 * used for identifies containers and its products.
 *
 * @author Rafael M. Salvioni
 * @see http://www.gtin.info/itf-14-barcodes/
 * @see http://www.activebarcode.com/codes/itf14.html
 */
class Itf14 extends Code2of5\Interleaved implements FixedLengthInterface
{
    /**
     * Create a new instance using Ean13 barcode given.
     * 
     * @param int $packId Package ID
     * @param Upc\Ean13 $ean13
     * @return self
     */
    public static function fromEan13($packId, Upc\Ean13 $ean13)
    {
        $data = $packId . $ean13->getRealData();
        return new self($data, false);
    }

    /**
     * Remove all spaces from $data.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        $data = \str_replace(' ', '', $data);
        parent::__construct($data, $hasChecksum);
    }
    
    /**
     * Returns the embedded Ean13 data.
     * 
     * @return Upc\Ean13
     */
    public function getEan13()
    {
        $data = $this->getDataPart(1, 12);
        return new Upc\Ean13($data, false);
    }
    
    /**
     * Always 14.
     * 
     * @return int
     */
    public function getLength()
    {
        return 14;
    }
    
    /**
     * 
     * @return string
     */
    public function getDataToDisplay()
    {
        return \mask('# ## ##### ##### #', $this->data);
    }
    
    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        $len = $this->getLength();
        if (\strlen($data) == $len && $data[0] != '9') {
            return parent::checkData($data);
        }
        return false;
    }
}
