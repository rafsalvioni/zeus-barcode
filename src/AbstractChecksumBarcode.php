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
            parent::__construct($data);
            $this->extractChecksum($this->data, $this->data);
        }
        else {
            parent::__construct($data);
        }
        $this->data = $this->checksumResolver($this->data, $hasChecksum);
    }
}
