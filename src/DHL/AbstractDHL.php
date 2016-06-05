<?php

namespace Zeus\Barcode\DHL;

use Zeus\Barcode\Code2of5\Interleaved;
use Zeus\Barcode\FixedLengthInterface;

/**
 * Abstract class to create Deutsche Post (DHL) barcodes.
 *
 * @author Rafael M. Salvioni
 */
abstract class AbstractDHL extends Interleaved implements FixedLengthInterface
{
    /**
     * Remove data mask from $data.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        $data = \preg_replace('/\. /', '', $data);
        parent::__construct($data, $hasChecksum);
    }

    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $data  = \str_split($data);
        $sum   = self::sumAlternateWeight($data, 4, 9);
        $check = (10 - ($sum % 10)) % 10;
        return $check;
    }
    
    /**
     * Returns a data field value.
     * 
     * @param int $field Constant
     * @return string
     */
    protected function getField($field)
    {
        return $this->getDataPart($field, self::$lengths[$field]);
    }
    
    /**
     * Create a new class instance with another field value.
     * 
     * @param int $field Constantes da classe
     * @param string|int $value Novo valor
     * @return self
     */
    protected function withField($field, $value)
    {
        $data = $this->withDataPart($value, $field, self::$lengths[$field]);
        return new self($data, false);
    }
}
