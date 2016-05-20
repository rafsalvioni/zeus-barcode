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
            if (empty($data) || $check != $this->calcChecksum($data)) {
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
     * Returns the checksum digit length.
     * 
     * By default, if checksum position is negative, returns position * -1.
     * Else, return 1.
     * 
     * Overload if necessary.
     * 
     * @return int
     */
    protected function getCheckLength()
    {
        $pos = $this->getCheckPosition();
        return $pos < 0 ? $pos * -1 : 1;
    }
    
    /**
     * Returns the checksum digit position.
     * 
     * By default, return always -1. Overload if necessary.
     * 
     * @return int
     */
    protected function getCheckPosition()
    {
        return -1;
    }
    
    /**
     * Checks if data have a minimal length considering the checksum digit
     * length.
     * 
     * @param string $data
     * @return bool
     */
    protected function checkLength($data)
    {
        return \strlen($data) > $this->getCheckLength();
    }

    /**
     * Inserts the checksum on a data.
     * 
     * Using getCheckLength() to padding zeros left on checksum and
     * getCheckPosition() to insert it.
     * 
     * @param string $data
     * @param string $checksum
     * @return string
     */
    protected function insertChecksum($data, $checksum = null)
    {
        $len      = $this->getCheckLength();
        $pos      = $this->getCheckPosition();
        $checksum = \str_pad($checksum, $len, '0', \STR_PAD_LEFT);
        
        if ($pos < 0) {
            $pos += $len;
            if ($pos == 0) {
                return $data . $checksum;
            }
        }
        else if ($pos == 0) {
            return $checksum . $data;
        }
        return \substr_replace($data, $checksum, $pos, 0);
    }
    
    /**
     * Extracts the checksum of data and return it.
     * 
     * Using getCheckLength() and getCheckPosition() to extract it.
     * 
     * @param string $data
     * @param string $cleanData Data without checksum
     * @return int
     */
    protected function extractChecksum($data, &$cleanData)
    {
        $len       = $this->getCheckLength();
        $pos       = $this->getCheckPosition();
        $checksum  = \substr_remove($data, $pos, $len);
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
    public function getRealData()
    {
        $data = null;
        $this->extractChecksum($this->data, $data);
        return $data;
    }
    
    /**
     * Extracts checksum from new data.
     * 
     * @param string $value
     * @param int $start
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function withDataPart($value, $start, $length)
    {
        $data = parent::withDataPart($value, $start, $length);
        $this->extractChecksum($data, $data);
        return $data;
    }
}
