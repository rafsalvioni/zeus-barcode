<?php

namespace Zeus\Barcode;

use Zeus\Barcode\Encoder\EncoderInterface;

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
        '0' => '101010011',    '1' => '101011001',   '2' => '101001011',
        '3' => '110010101',    '4' => '101101001',   '5' => '110101001',
        '6' => '100101011',    '7' => '100101101',   '8' => '100110101',
        '9' => '110100101',    '-' => '101001101',   '$' => '101100101',
        ':' => '1101011011',   '/' => '1101101011',  '.' => '1101101101',
        '+' => '101100110011', 'A' => '1011001001',  'B' => '1001001011',
        'C' => '1010010011',   'D' => '1010011001',
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
     * @param string $bin
     * @return self
     * @throws CodabarException
     */
    public static function fromBinary($bin)
    {
        $data   = '';
        $table  =& self::$encodingTable;
        
        $decode = function($binChar, $throws = true) use (&$table)
        {
            $char = \array_search($binChar, $table);
            if ($char === false && $throws) {
                throw new CodabarException('Unknown binary char!');
            }
            return $char;
        };
        
        $start = \substr_remove($bin, 0, 10);
        $bin   = \substr($bin, 1);
        $stop  = \substr_remove($bin, -10);
        
        while (\preg_match('/^(1[01]{8})/', $bin, $match)) {
            for ($i = 0; $i < 4; $i++) {
                $char = $decode($match[0] . \substr($bin, 9, $i), false);
                if ($char !== false) {
                    $data .= $char;
                    $bin   = \substr($bin, 10 + $i);
                    continue 2;
                }
            }
            throw new CodabarException('Unknown binary char: ' . $match[0]);
        }
        if (!empty($bin)) {
            throw new CodabarException('Invalid binary string!');
        }
        
        $class = \get_called_class();
        return new $class($decode($start) . $data . $decode($stop));
    }
    
    /**
     * Uses the start and stop chars if they given.
     * 
     * @param string $data
     */
    public function __construct($data)
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
        parent::__construct($data);
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
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return \preg_match("/^[0-9\\-\\$\\:\\/\\.\\+]+$/", $data);
    }

    /**
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    protected function encodeData(EncoderInterface &$encoder, $data)
    {
        $encoded = '';
        $data    = \str_split($data);
        
        \array_unshift($data, $this->start);
        \array_push($data, $this->stop);
        
        foreach ($data as &$char) {
            $encoded .= self::$encodingTable[$char] . '0';
        }
        
        $encoded = \substr($encoded, 0, -1);
        $encoder->addBinary($encoded);
    }
}

/**
 * Class' exception
 * 
 */
class CodabarException extends Exception {}

