<?php

namespace Zeus\Barcode\Code39;

use Zeus\Barcode\AbstractBarcode;
use Zeus\Barcode\TwoWidthInterface;
use Zeus\Barcode\TwoWidthTrait;
use Zeus\Barcode\ChecksumInterface;

/**
 * Implementation of Code39 barcode standard, without checksum.
 * 
  * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code39.phtml
 */
class Code39 extends AbstractBarcode implements TwoWidthInterface
{
    use TwoWidthTrait;

    /**
     * Encoding table
     * 
     * N -> narrow
     * W -> wide
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => 'NNNWWNWNN', '1' => 'WNNWNNNNW',	'2' => 'NNWWNNNNW',
        '3' => 'WNWWNNNNN', '4' => 'NNNWWNNNW',	'5' => 'WNNWWNNNN',
        '6' => 'NNWWWNNNN', '7' => 'NNNWNNWNW',	'8' => 'WNNWNNWNN',
        '9' => 'NNWWNNWNN', 'A' => 'WNNNNWNNW',	'B' => 'NNWNNWNNW',
        'C' => 'WNWNNWNNN', 'D' => 'NNNNWWNNW', 'E' => 'WNNNWWNNN',
        'F' => 'NNWNWWNNN', 'G' => 'NNNNNWWNW', 'H' => 'WNNNNWWNN',
        'I' => 'NNWNNWWNN', 'J' => 'NNNNWWWNN', 'K' => 'WNNNNNNWW',
        'L' => 'NNWNNNNWW', 'M' => 'WNWNNNNWN', 'N' => 'NNNNWNNWW',
        'O' => 'WNNNWNNWN', 'P' => 'NNWNWNNWN', 'Q' => 'NNNNNNWWW',
        'R' => 'WNNNNNWWN', 'S' => 'NNWNNNWWN', 'T' => 'NNNNWNWWN',
        'U' => 'WWNNNNNNW', 'V' => 'NWWNNNNNW', 'W' => 'WWWNNNNNN',
        'X' => 'NWNNWNNNW', 'Y' => 'WWNNWNNNN', 'Z' => 'NWWNWNNNN',
        '-' => 'NWNNNNWNW', '.' => 'WWNNNNWNN', ' ' => 'NWWNNNWNN',
        '$' => 'NWNWNWNNN', '/' => 'NWNWNNNWN', '+' => 'NWNNNWNWN',
        '%' => 'NNNWNWNWN', '*' => 'NWNNWNWNN',
    ];
    
    /**
     * Try to return the best Code39 instance according given parameters.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @param bool $useChecksum If don't have checksum, you want use it?
     * @return Code39
     * @throws \Zeus\Barcode\Exception
     */
    public static function factory($data, $hasChecksum = false, $useChecksum = false)
    {
        $classes = [];
        if ($hasChecksum || $useChecksum) {
            $classes = [Code39Mod43::class, Code39ExtMod43::class];
        }
        else {
            $classes = [Code39::class, Code39Ext::class];
        }
        foreach ($classes as &$class) {
            try {
                return new $class($data, $hasChecksum);
            }
            catch (\Exception $ex) {
                //noop
            }
        }
        throw $ex;
    }
    
    /**
     * Helper function to calculate mod43 checksum.
     * 
     * @param string $data
     * @return string
     */
    protected static function mod43($data)
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

    /**
     * 
     * @param string $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setWideWidth(2);
    }
    
    /**
     * Return the current barcode to its version with checksum.
     * 
     * @return Code39Mod43|Code39ExtMod43
     */
    public function withChecksum()
    {
        if ($this instanceof ChecksumInterface) {
            return $this;
        }
        else {
            $class = \get_class($this) . 'Mod43';
            return new $class($this->data, false);
        }
    }

    /**
     * Return the current barcode to its version without checksum.
     * 
     * @return Code39|Code39Ext
     */
    public function withoutChecksum()
    {
        if (!($this instanceof ChecksumInterface)) {
            return $this;
        }
        else {
            $class = \substr(\get_class($this), 0, -5);
            return new $class($this->getRawData());
        }
    }
    
    /**
     * Return the current barcode to its version using full ASCII mode.
     * 
     * @return Code39Ext|Code39ExtMod43
     */
    public function toExtended()
    {
        if ($this instanceof Code39Ext) {
            return $this;
        }
        else {
            $class = \get_class($this);
            if (\substr($class, -5) == 'Mod43') {
                $class = \substr_replace($class, 'Ext', -5, 0);
            }
            else {
                $class .= 'Ext';
            }
            if ($this instanceof ChecksumInterface) {
                return new $class($this->getRawData(), false);
            }
            else {
                return new $class($this->data);
            }
        }
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return \preg_match('/^[A-Z\d\-\. \$\/\+\%]+$/', $data);
    }
    
    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $data    = "*$data*";
        $data    = \str_split($data);
        $encoded = '';
        
        foreach ($data as &$char) {
            $encoded .= $this->encodeChar($char);
            $encoded .= $this->encodeWithWidth(true, false);
        }
        
        $encoded = \substr($encoded, 0, $this->narrowWidth * -1);
        
        return $encoded;
    }
    
    /**
     * Encodes a single char.
     * 
     * @param string $char
     * @return string
     */
    protected function encodeChar($char)
    {
        $encChar =& self::$encodingTable[$char];
        $bar     = true;
        $return  = '';
        
        for ($i = 0; $i < 9; $i++) {
            $return .= $this->encodeWithWidth($encChar{$i} == 'N', $bar);
            $bar     = !$bar;
        }
        
        return $return;
    }
}
