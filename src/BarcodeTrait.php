<?php

namespace Zeus\Barcode;

use Zeus\Barcode\Encoder\EncoderInterface;
use Zeus\Barcode\Encoder\BarSpace;

/**
 * Trait to implements some methods of BarcodeInterface.
 *
 * @author Rafael M. Salvioni
 */
trait BarcodeTrait
{
    /**
     * Stores the barcode data.
     * 
     * @var string
     */
    protected $data;
    /**
     * Stores the encoded object. Its a cache. Use getEncoded().
     * 
     * @var EncoderInterface
     */
    protected $encoded;

    /**
     * Auxiliar function to calculate checksums alterning between weights.
     * 
     * The order is from right to left.
     * 
     * Returns the sum result.
     * 
     * @param array $data Array of integers
     * @param int $firstWeight
     * @param int $secWeight
     * @return int
     */
    protected static function sumAlternateWeight(array $data, $firstWeight, $secWeight)
    {
        $sum    = 0;
        $weight = $firstWeight;
        while (!empty($data)) {
            $sum   += $weight * (int)\array_pop($data);
            $weight = $weight == $firstWeight ? $secWeight : $firstWeight;
        }
        return $sum;
    }
    
    /**
     * Auxiliar function to calculate checksums using cresacent weights.
     * 
     * The order is from right to left.
     * 
     * Returns the sum result.
     * 
     * @param array $data Array of integers
     * @param int $minWeight
     * @param int $maxWeight
     * @return int
     */
    protected static function sumCrescentWeight(array $data, $minWeight, $maxWeight = null)
    {
        $sum    = 0;
        $weight = $minWeight;
        while (!empty($data)) {
            $sum += $weight++ * (int)\array_pop($data);
            if ($maxWeight !== null && $weight > $maxWeight) {
                $weight = $minWeight;
            }
        }
        return $sum;
    }

    /**
     * Padding zeros on left.
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
     * 
     * @return EncoderInterface
     */
    public function getEncoded()
    {
        if (empty($this->encoded)) {
            $this->encoded = new BarSpace();
            $this->encodeData($this->encoded, $this->data);
            $this->encoded->close();
        }
        return $this->encoded;
    }
    
    /**
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
            return $data;
        }
        throw new Exception('Wrong data part length!');
    }
    
    /**
     * Encodes a data and put them on Encoder given.
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    abstract protected function encodeData(EncoderInterface &$encoder, $data);
}
