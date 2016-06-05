<?php

namespace Zeus\Barcode\DHL;

use Zeus\Barcode\FieldsTrait;

/**
 * Implementation of Identcode barcode standard.
 * 
 * Leitcode is a Interleaved 2 of 5 barcode with 12 fixed digits.
 * 
 * @author Rafael M. Salvioni
 * @see http://www.activebarcode.com/codes/identcode.html
 */
class Identcode extends AbstractDHL
{
    /**
     * 
     */
    const MAIL_CENTER     = 0;
    /**
     * 
     */
    const CUSTOMER        = 2;
    /**
     * 
     */
    const DELIVERY_NUMBER = 5;
    
    /**
     *
     * @var array
     */
    protected static $lengths = [
        self::MAIL_CENTER     => 2,
        self::CUSTOMER        => 3,
        self::DELIVERY_NUMBER => 6,
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
        $mailCenter, $customerCode, $deliveryNumber
    ){
        $me = new self('0', false);
        return $me->withMailCenter($mailCenter)
                  ->withCustomerCode($customerCode)
                  ->withDeliveryNumber($deliveryNumber);
    }
    
    /**
     * Returns barcode's mail center code.
     * 
     * @return string
     */
    public function getMailCenter()
    {
        return $this->getField(self::MAIL_CENTER);
    }
    
    /**
     * Create a new class instance with another mail center code.
     * 
     * @param string|int $code
     * @return self
     */
    public function withMailCenter($code)
    {
        return $this->withField(self::MAIL_CENTER, $code);
    }

    /**
     * Returns barcode's customer code.
     * 
     * @return string
     */
    public function getCustomerCode()
    {
        return $this->getField(self::CUSTOMER);
    }
    
    /**
     * Create a new class instance with another customer code.
     * 
     * @param string|int $code
     * @return self
     */
    public function withCustomerCode($code)
    {
        return $this->withField(self::CUSTOMER, $code);
    }

    /**
     * Returns barcode's delivery number.
     * 
     * @return string
     */
    public function getDeliveryNumber()
    {
        return $this->getField(self::DELIVERY_NUMBER);
    }
    
    /**
     * Create a new class instance with another delivery number.
     * 
     * @param string|int $number
     * @return self
     */
    public function withDeliveryNumber($number)
    {
        return $this->withField(self::DELIVERY_NUMBER, $number);
    }

    /**
     * Always 14.
     * 
     * @return int
     */
    public function getLength()
    {
        return 12;
    }
    
    /**
     * 
     * @return string
     */
    public function getDataToDisplay()
    {
        return \preg_replace(
                    '/^(\d{2})(\d{3})(\d{3})(\d{3})(\d)$/',
                    '$1.$2 $3.$4 $5',
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
