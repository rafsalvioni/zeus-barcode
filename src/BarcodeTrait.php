<?php

namespace Zeus\Barcode;

/**
 * Trait to implement some methods of BarcodeInterface.
 *
 * @author Rafael M. Salvioni
 */
trait BarcodeTrait
{
    /**
     * Barcode data, with checksum if has one
     * 
     * @var string
     */
    protected $data;
    /**
     * Stores only the checksum. Its a cache. Use getChecksum()
     * 
     * @var string
     */
    protected $checksum;

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    public static function check($data, $hasChecksum = true)
    {
        $class = \get_called_class();
        try {
            new $class($data, $hasChecksum);
            return true;
        }
        catch (Exception $ex) {
            return false;
        }
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
    
    /**
     * Returns a subpart of barcode data.
     * 
     * @param int $start
     * @param int $length
     * @return string
     */
    public function getDataPart($start, $length = null)
    {
        return \substr($this->data, $start, $length);
    }
    
    /**
     * Makes a new data replacing a part of current data for another.
     * 
     * $value will be padded with left zeros if its length is less than $length.
     * If $value length is greater than $length, a exception will be throw.
     * 
     * New $data returned will be without checksum.
     * 
     * @param string $value
     * @param int $start
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function withDataPart($value, $start, $length)
    {
        $value = \str_pad($value, $length, '0', \STR_PAD_LEFT);
        if (\strlen($value) == $length) {
            $data = \substr_replace($this->data, $value, $start, $length);
            $this->extractChecksum($data, $data);
            return $data;
        }
        throw new Exception('Wrong data part length!');
    }

    /**
     * 
     * @param bool $withChecksum
     * @return string
     */
    public function getData($withChecksum = true)
    {
        if ($withChecksum) {
            return $this->data;
        }
        else {
            $foo = null;
            $this->extractChecksum($this->data, $foo);
            return $foo;
        }
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
    public function getPrintableData()
    {
        return $this->getData(true);
    }

    /**
     * On serialize, use only "data" attribute.
     * 
     * @return string[]
     */
    public function __sleep()
    {
        return ['data'];
    }
}
