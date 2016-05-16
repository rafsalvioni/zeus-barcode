<?php

namespace Zeus\Barcode;

/**
 * Description of AbstractChecksumBarcode
 *
 * @author Rafael
 */
abstract class AbstractChecksumBarcode extends AbstractBarcode implements
    ChecksumBarcodeInterface
{
    use ChecksumBarcodeTrait;
    
    /**
     * Calculates a barcode's checksum with a given data.
     * 
     * @param string $data
     * @return string
     */
    abstract protected function calcChecksum($data);
    
    /**
     * $data will be validated in constructor. If $data don't have a checksum,
     * it will generated. Else, data's checksum will be validated too.
     * 
     * @param string $data
     * @param bool $hasChecksum Indicates if $data has a builtin checksum
     * @throws Exception If data or checksum is invalid
     */
    public function __construct($data, $hasChecksum = true)
    {
        if (!$hasChecksum) {
            $data = $this->insertChecksum($data, '0');
        }
        
        parent::__construct($data);
        
        $check = $this->extractChecksum($this->data, $this->data);
        $calc  = $this->calcChecksum($this->data);
        
        if ($hasChecksum && $check != $calc) {
            throw $this->createException('Invalid "%class%" checksum!');
        }
        
        $this->data = $this->insertChecksum($this->data, $calc);
    }
}
