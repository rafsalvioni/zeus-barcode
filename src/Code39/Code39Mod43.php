<?php

namespace Zeus\Barcode\Code39;

use Zeus\Barcode\ChecksumInterface;
use Zeus\Barcode\ChecksumTrait;

/**
 * Implementation of Code39 barcode standard, with mod43 checksum.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code39.phtml
 */
class Code39Mod43 extends Code39 implements ChecksumInterface
{
    use ChecksumTrait;

    /**
     * If data has extended ascii chars, instance will be work in extended mode.
     * 
     * Otherwise, if data have chars used in both tables (ex: %, $ etc.), you
     * can use $forceExtended to encode these chars using extended ascii.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @param bool $forceExtended
     */
    public function __construct($data, $hasChecksum = false, $forceExtended = false)
    {
        parent::__construct($data, $forceExtended);
        $this->data = $this->checksumResolver($data, $hasChecksum);
    }
    
    /**
     * Returns a Code39 instance, without checksum, basead on current barcode.
     * 
     * @return Code39
     */
    public function withoutChecksum()
    {
        $data = null;
        $this->extractChecksum($this->data, $data);
        return new parent($data, $this->useExt);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function calcChecksum($data)
    {
        $data = $this->toExtended($data);
        $data = \str_split($data);
        $enc  = \array_keys(self::$encodingTable);
        $flip = \array_flip($enc);
        $sum  = 0;
        foreach ($data as &$char) {
            $code = $flip[$char];
            $sum += $code;
        }
        return $enc[($sum % 43)];
    }
}
