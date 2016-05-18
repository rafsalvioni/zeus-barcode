<?php

namespace Zeus\Barcode\Code39;

use Zeus\Barcode\ChecksumInterface;
use Zeus\Barcode\ChecksumTrait;

/**
 * Implementation of Code39 barcode standard, using full ASCII mode and
 * mod43 checksum.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code39.phtml
 */
class Code39ExtMod43 extends Code39Ext implements ChecksumInterface
{
    use ChecksumTrait;

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @param bool $forceExtended
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
        $data = $this->resolveExtended($data);
        return self::mod43($data);
    }
}
