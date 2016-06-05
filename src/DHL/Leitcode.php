<?php

namespace Zeus\Barcode\DHL;

/**
 * Implementation of Leitcode barcode standard.
 * 
 * Leitcode is a Interleaved 2 of 5 barcode with 14 fixed digits length.
 * 
 * @author Rafael M. Salvioni
 * @see http://www.activebarcode.com/codes/leitcode.html
 */
class Leitcode extends AbstractDHL
{
    /**
     * 
     */
    const ZIP_CODE     = 0;
    /**
     * 
     */
    const STREET_CODE  = 5;
    /**
     * 
     */
    const HOUSE_NUMBER = 8;
    /**
     * 
     */
    const PRODUCT_CODE = 11;
    
    /**
     *
     * @var array
     */
    protected static $lengths = [
        self::ZIP_CODE     => 5,
        self::STREET_CODE  => 3,
        self::HOUSE_NUMBER => 3,
        self::PRODUCT_CODE => 2,
    ];
    
    /**
     * Builder to create a instance using data fields.
     * 
     * @param string|int $zipCode
     * @param string|int $streetCode
     * @param string|int $houseNumber
     * @param string|int $productCode
     * @return self
     */
    public static function builder(
        $zipCode, $streetCode, $houseNumber, $productCode
    ){
        $me = new self('0', false);
        return $me->withZipCode($zipCode)
                  ->withStreetCode($streetCode)
                  ->withHouseNumber($houseNumber)
                  ->withProductCode($productCode);
    }
    
    /**
     * Returns barcode's mail zip code.
     * 
     * @return string
     */
    public function getZipCode()
    {
        return $this->getField(self::ZIP_CODE);
    }
    
    /**
     * Create a new class instance with another zip code.
     * 
     * @param string|int $code
     * @return self
     */
    public function withZipCode($code)
    {
        return $this->withField(self::ZIP_CODE, $code);
    }
    
    /**
     * Returns barcode's street code.
     * 
     * @return string
     */
    public function getStreetCode()
    {
        return $this->getField(self::STREET_CODE);
    }
    
    /**
     * Create a new class instance with another street code.
     * 
     * @param string|int $code
     * @return self
     */
    public function withStreetCode($code)
    {
        return $this->withField(self::STREET_CODE, $code);
    }
    /**
     * Returns barcode's house number.
     * 
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->getField(self::HOUSE_NUMBER);
    }
    
    /**
     * Create a new class instance with another house number.
     * 
     * @param string|int $number
     * @return self
     */
    public function withHouseNumber($number)
    {
        return $this->withField(self::HOUSE_NUMBER, $number);
    }
    /**
     * Returns barcode's product code.
     * 
     * @return string
     */
    public function getProductCode()
    {
        return $this->getField(self::PRODUCT_CODE);
    }
    
    /**
     * Create a new class instance with another product code.
     * 
     * @param string|int $code
     * @return self
     */
    public function withProductCode($code)
    {
        return $this->withField(self::PRODUCT_CODE, $code);
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
        return \preg_replace(
                    '/^(\d{5})(\d{3})(\d{3})(\d{2})(\d)$/',
                    '$1.$2.$3.$4 $5',
                    $this->data
                );
    }
    
    /**
     * 
     * @param int $field
     * @return int
     */
    protected function getFieldLength($field)
    {
        return self::$lengths[$field];
    }
}
