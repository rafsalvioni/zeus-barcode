<?php

namespace Zeus\Barcode\Upc;

use Zeus\Barcode\AbstractBarcode;
use Zeus\Barcode\FixedLengthInterface;
use Zeus\Barcode\Encoder\EncoderInterface;

/**
 * Implements a EAN-5 supplemental barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/upcext.phtml
 */
class Ean5 extends AbstractBarcode implements FixedLengthInterface
{
    use EanHelperTrait;
    
    /**
     * Parity table
     * 
     * 0 => Odd
     * 1 => Even
     * 
     * @var array
     */
    protected static $parityTable = [
        '0' => [1, 1, 0, 0, 0], '1' => [1, 0, 1, 0, 0],
        '2' => [1, 0, 0, 1, 0], '3' => [1, 0, 0, 0, 1],
        '4' => [0, 1, 1, 0, 0], '5' => [0, 0, 1, 1, 0],
        '6' => [0, 0, 0, 1, 1], '7' => [0, 1, 0, 1, 0],
        '8' => [0, 1, 0, 0, 1], '9' => [0, 0, 1, 0, 1],
    ];
    
    /**
     * 
     * @param string $bin
     * @return self
     * @throws Ean5Exception
     */
    public static function fromBinary($bin)
    {
        if (\preg_match('/^1011([01]{43})$/', $bin, $match)) {
            $bin = $match[1];
            unset($match);
        }
        else {
            throw new Ean5Exception('Invalid binary string!');
        }
        
        $bin  = \str_split($bin, 9);
        $data = '';
        $ptab = [];
        
        foreach ($bin as &$binChar) {
            $binChar = \substr($binChar, 0, 7);
            $data   .= self::decode($binChar, $ptab, [0, 1]);
        }
        
        $p       = \array_search($ptab, self::$parityTable);
        $dataArr = \str_split($data);
        if ($p !== false && $p == \substr(self::sumAlternateWeight($dataArr, 3, 9), -1)) {
            $class = \get_called_class();
            return new $class($data);
        }
        throw new Ean5Exception('Invalid binary encode');
    }
    
    /**
     * Returns 5.
     * 
     * @return int
     */
    public function getLength()
    {
        return 5;
    }

    /**
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    protected function encodeData(EncoderInterface &$encoder, $data)
    {
        $data      = \str_split($data);
        $check     = \substr(self::sumAlternateWeight($data, 3, 9), -1);
        $parityTab =& self::$parityTable[$check];
        $encoded   = '1011';
                    
        foreach ($data as $i => &$char) {
            $encoded .= self::$encodingTable[$char][$parityTab[$i]];
            $encoded .= '01';
        }
        
        $encoded   = \substr($encoded, 0, -2);
        $encoder->addBinary($encoded);
    }
}

/**
 * Class' exception
 * 
 */
class Ean5Exception extends Exception {}
