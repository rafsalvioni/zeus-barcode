<?php

namespace Zeus\Barcode\Code39;

use Zeus\Barcode\ChecksumInterface;
use Zeus\Barcode\ChecksumTrait;

/**
 * Implementation of Code39 with mod43 checksum.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code39.phtml
 */
class Code39Mod43 extends Code39 implements ChecksumInterface
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
        $this->data = $this->checksumResolver($data, $hasChecksum);
    }
    
    /**
     * 
     * @param string $data
     * @return string
     */
    protected function calcChecksum($data)
    {
        return self::mod43($data);
    }
    
    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        if (parent::checkData($data)) {
            return $this->checkLength($data);
        }
        return false;
    }
}
