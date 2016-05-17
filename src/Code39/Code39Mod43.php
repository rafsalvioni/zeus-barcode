<?php

namespace Zeus\Barcode\Code39;

/**
 * Implementation of Code39 barcode standard, with mod43 checksum.
 *
 * @author Rafael M. Salvioni
 */
class Code39Mod43 extends Code39 implements ChecksumInterface
{
    use ChecksumTrait;

    /**
     * 
     * @param type $data
     * @param type $hasChecksum
     */
    public function __construct($data, $hasChecksum = false)
    {
        parent::__construct($data);
        $this->data = $this->checksumResolver($data, $hasChecksum);
    }
    
    /**
     * Returns a Code39 instance basead on current barcode.
     * 
     * @return Code39
     */
    public function withoutChecksum()
    {
        $data = null;
        $this->extractChecksum($this->data, $data);
        return new parent($data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function calcChecksum($data)
    {
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
