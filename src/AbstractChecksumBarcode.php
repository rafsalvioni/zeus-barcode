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
        parent::__construct($hasChecksum ? $data : $data . '0');
        if (!$hasChecksum) {
            $this->data = \substr($this->data, 0, -1);
        }
        $this->data = $this->checksumResolver($this->data, $hasChecksum);
    }
}
