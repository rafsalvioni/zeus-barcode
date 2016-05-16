<?php

namespace Zeus\Barcode;

/**
 * Abstract barcode that implements ChecksumInterface.
 *
 * @author Rafael M. Salvioni
 */
abstract class AbstractChecksumBarcode extends AbstractBarcode implements
    ChecksumInterface
{
    use ChecksumTrait;
    
    /**
     *  
     * @param string $data
     * @param bool $hasChecksum
     * @throws Exception
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
            throw $this->createException('Invalid "%class%" barcode checksum!');
        }
        $this->data = $this->insertChecksum($this->data, $calc);
    }
    
    /**
     * Calculates the checksum.
     * 
     * @return int
     */
    abstract protected function calcChecksum($data);
}
