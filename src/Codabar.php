<?php

namespace Zeus\Barcode;

/**
 * Implements a Codabar barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/codabar.phtml
 */
class Codabar extends AbstractBarcode
{
    /**
     * Encoding table
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => '101010011',
        '1' => '101011001',
        '2' => '101001011',
        '3' => '110010101',
        '4' => '101101001',
        '5' => '110101001',
        '6' => '100101011',
        '7' => '100101101',
        '8' => '100110101',
        '9' => '110100101',
        '-' => '101001101',
        '$' => '101100101',
        ':' => '1101011011',
        '/' => '1101101011',
        '.' => '1101101101',
        '+' => '101100110011',
        'A' => '1011001001',
        'B' => '1001001011',
        'C' => '1010010011',
        'D' => '1010011001',
    ];
    
    /**
     * Start char
     * 
     * @var string 
     */
    protected $start;
    /**
     * Stop char
     * 
     * @var string
     */
    protected $stop;
    
    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        if (\preg_match('/^([A-D])(.+?)([A-D])$/', $data, $match)) {
            $this->start = $match[1];
            $this->stop  = $match[3];
            $data        = $match[2];
        }
        else {
            $this->start = 'A';
            $this->stop  = 'B';
        }
        parent::__construct($data, $hasChecksum);
    }
    
    /**
     * 
     * @return string
     */
    public function getPrintableData()
    {
        $string = parent::getPrintableData();
        return $this->start . $string . $this->stop;
    }

    /**
     * This barcode doesn't have checksum...
     * 
     * @param string $data
     */
    protected function calcChecksum($data)
    {
        //n.a
    }
    
    /**
     * 
     * @param string $data
     * @param int $checksum
     * @return string
     */
    protected function insertChecksum($data, $checksum)
    {
        return $data;
    }
    
    /**
     * 
     * @param string $data
     * @param mixed $cleanData
     * @return null
     */
    protected function extractChecksum($data, &$cleanData)
    {
        $cleanData = $data;
        return null;
    }

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        return \preg_match("/^[0-9\\-\\$\\:\\/\\.\\+]+$/", $data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $encoded = '';
        $data    = \str_split($data);
        
        \array_unshift($data, $this->start);
        \array_push($data, $this->stop);
        
        foreach ($data as &$char) {
            $encoded .= self::$encodingTable[$char] . '0';
        }
        
        $encoded = \substr($encoded, 0, -1);
        
        return $encoded;
    }
}
