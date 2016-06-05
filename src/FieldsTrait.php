<?php

namespace Zeus\Barcode;

/**
 * Trait to helper barcodes that have variable length fields.
 *
 * @author Rafael M. Salvioni
 */
trait FieldsTrait
{
    /**
     * Return a field length.
     * 
     * @param int $field
     * @return int
     */
    abstract protected function getFieldLength($field);
    
    /**
     * Returns a data field value.
     * 
     * @param int $field Constant
     * @return string
     */
    protected function getField($field)
    {
        $len = $this->getFieldLength($field);
        return $this->getDataPart($field, $len);
    }
    
    /**
     * Create a new class instance with another field value.
     * 
     * @param int $field Field constant
     * @param string|int $value
     * @return self
     */
    protected function withField($field, $value)
    {
        $len   = $this->getFieldLength($field);
        $data  = $this->withDataPart($value, $field, $len);
        $class = \get_called_class();
        return new $class($data, false);
    }
}
