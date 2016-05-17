<?php

namespace Zeus\Barcode;

/**
 * Trait to implement methods of ChecksumInterface.
 * 
 * It should be used in conjunction of BarcodeTrait.
 *
 * @author Rafael M. Salvioni
 */
trait ChecksumTrait
{
    /**
     * Stores the checksum. Its a cache. Use getChecksum().
     * 
     * @var int
     */
    protected $checksum;
    
    /**
     * Check a data with checksum or create the checksum and put it on data.
     * 
     * Returns the data with checksum.
     * 
     * If checksum is invalid, a exception will be thrown.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return string
     * @throws Exception
     */
    protected function checksumResolver($data, $hasChecksum)
    {
        if ($hasChecksum) {
            $check = $this->extractChecksum($data, $data);
            if ($check != $this->calcChecksum($data)) {
                throw $this->createException('Invalid "%class%" barcode checksum!');
            }
        }
        else {
            $check = $this->calcChecksum($data);
        }
        return $this->insertChecksum($data, $check);
    }
    
    /**
     * Calculates the checksum.
     * 
     * @return int
     */
    abstract protected function calcChecksum($data);

    /**
     * Inserts the checksum on a data.
     * 
     * By default, checksum is put on end of data. Overload if necessary.
     * 
     * @param string $data
     * @param int $checksum
     * @return string
     */
    protected function insertChecksum($data, $checksum)
    {
        return $data . $checksum;
    }
    
    /**
     * Extracts the checksum of data and return it.
     * 
     * By default, expects checksum on end of data. Overload if necessary.
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
     * 
     * @return string
     */
    public function getRawData()
    {
        $data = null;
        $this->extractChecksum($this->data, $data);
        return $data;
    }
}
