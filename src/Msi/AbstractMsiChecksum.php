<?php

namespace Zeus\Barcode\Msi;

use Zeus\Barcode\ChecksumInterface;
use Zeus\Barcode\ChecksumTrait;

/**
 * Abstract implementation of MSI using checksum.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/msi.phtml
 */
abstract class AbstractMsiChecksum extends Msi implements ChecksumInterface
{
    use ChecksumTrait;
    
    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        parent::__construct($data);
        $this->data = $this->checksumResolver($this->data, $hasChecksum);
    }
    
    /**
     * Create a instance of Msi without checksum.
     * 
     * @return Msi
     */
    public function withoutChecksum()
    {
        $data = null;
        $this->extractChecksum($this->data, $data);
        return new Msi($data);
    }
}
