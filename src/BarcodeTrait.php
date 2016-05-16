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
     * 
     * @param string $data
     * @return bool
     */
    public static function check($data)
    {
        $class = \get_called_class();
        try {
            new $class($data);
            return true;
        }
        catch (Exception $ex) {
            return false;
        }
    }
    
    /**
     * Padding zeros left to necessary length.
     * 
     * @param string $data
     * @param int $length
     * @return string
     */
    protected static function zeroLeftPadding($data, $length)
    {
        return \str_pad($data, $length, '0', \STR_PAD_LEFT);
    }

    /**
     * Return the sum of products from $data chars using weights given.
     * 
     * @param string $data Numerical data
     * @param int $firstWeight Weight for odd positions
     * @param int $secWeight Weight for even positions
     * @return int
     */
    protected static function calcSumCheck($data, $firstWeight = 3, $secWeight = 1)
    {
        $len    = \strlen($data);
        $sum    = 0;
        $weight = $firstWeight;
        
        for ($i = $len - 1; $i >= 0; $i--) {
            $sum   += $weight * (int)$data{$i};
            $weight = $weight == $firstWeight ? $secWeight : $firstWeight;
        }
        
        return $sum;
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
        $value = self::zeroLeftPadding($value, $length);
        if (\strlen($value) == $length) {
            $data = \substr_replace($this->data, $value, $start, $length);
            $this->extractChecksum($data, $data);
            return $data;
        }
        throw new Exception('Wrong data part length!');
    }

    /**
     * 
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * 
     * @return string
     */
    public function getPrintableData()
    {
        return $this->getData();
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
