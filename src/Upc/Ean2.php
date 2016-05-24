<?php

namespace Zeus\Barcode\Upc;

use Zeus\Barcode\AbstractBarcode;
use Zeus\Barcode\FixedLengthInterface;
use Zeus\Barcode\Encoder\EncoderInterface;

/**
 * Implements a EAN-2 supplemental barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/upcext.phtml
 */
class Ean2 extends AbstractBarcode implements FixedLengthInterface
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
        '0' => [0, 0], '1' => [0, 1], '2' => [1, 0], '3' => [1, 1],
    ];
    
    /**
     * 
     * @param string $bin
     * @return self
     * @throws Ean2Exception
     */
    public static function fromBinary($bin)
    {
        if (!\preg_match('/^1011([01]{7})01([01]{7})$/', $bin, $match)) {
            throw new Ean2Exception('Invalid binary string!');
        }
        
        $ptab = [];
        $data = self::decode($match[1], $ptab, [0, 1]) .
                self::decode($match[2], $ptab, [0, 1]);
        
        $p = \array_search($ptab, self::$parityTable);
        if ($p !== false && ($data % 4) == $p) {
            $class = \get_called_class();
            return new $class($data);
        }
        throw new Ean2Exception('Invalid binary encode');
    }

    /**
     * Returns 2.
     * 
     * @return int
     */
    public function getLength()
    {
        return 2;
    }

    /**
     * 
     * @param EncoderInterface $bars
     * @param string $data
     */
    protected function encodeData(EncoderInterface &$encoder, $data)
    {
        $encoded   = '';
        $parityTab =& self::$parityTable[($data % 4)];
        
        $encoded  = '1011' .
                    self::$encodingTable[$data{0}][$parityTab[0]] .
                    '01' .
                    self::$encodingTable[$data{1}][$parityTab[1]];
        
        $encoder->addBinary($encoded);
    }
}

/**
 * Class' exception
 * 
 */
class Ean2Exception extends Exception {}

