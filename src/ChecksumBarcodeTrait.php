<?php

namespace Zeus\Barcode;

/**
 * Trait to implement methods of ChecksumBarcodeInterface.
 *
 * @author Rafael M. Salvioni
 */
trait ChecksumBarcodeTrait
{
    /**
     * Stores only the checksum. Its a cache. Use getChecksum()
     * 
     * @var string
     */
    protected $checksum;
    
    /**
     * 
     * @return string
     */
    public function getDataWithoutChecksum()
    {
        $foo = null;
        $this->extractChecksum($this->data, $foo);
        return $foo;
    }
    
    /**
     * 
     * @return int
     */
    public function getChecksum()
    {
        if (\is_null($this->checksum)) {
            $foo = null;
            $this->checksum = $this->extractChecksum($this->data, $foo);
        }
        return $this->checksum;
    }
    
    /**
     * Extract the checksum from a data.
     * 
     * @param string $data
     * @param string $cleanData Data without checksum
     * @return int 
     */
    protected function extractChecksum($data, &$cleanData)
    {
        $checksum  = \substr_remove($data, -1);
        $cleanData = $data;
        return $checksum;
    }
    
    /**
     * Insert a checksum on a data.
     * 
     * @param string $data
     * @param int $checksum
     * @return string
     */
    protected function insertChecksum($data, $checksum)
    {
        return $data . $checksum;
    }
}
