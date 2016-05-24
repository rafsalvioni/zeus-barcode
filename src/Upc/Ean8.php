<?php

namespace Zeus\Barcode\Upc;

use Zeus\Barcode\AbstractChecksumBarcode;
use Zeus\Barcode\FixedLengthInterface;
use Zeus\Barcode\Encoder\EncoderInterface;

/**
 * Implements a EAN-8 barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean8.phtml
 */
class Ean8 extends AbstractChecksumBarcode implements FixedLengthInterface
{
    use EanHelperTrait;
    
    /**
     * 
     * @param string $bin
     * @return self
     * @throws Ean8Exception
     */
    public static function fromBinary($bin)
    {
        if (\preg_match('/^101([01]{28})01010([01]{28})101$/', $bin, $match)) {
            $bin = $match[1] . $match[2];
            unset($match);
        }
        else {
            throw new Ean8Exception('Invalid binary string!');
        }
        
        $bin  = \str_split($bin, 7);
        $data = '';
        $ptab = [];
        
        foreach ($bin as $i => &$binChar) {
            $p     = $i < 4 ? [0] : [2];
            $data .= self::decode($binChar, $ptab, $p);
        }
        
        $class = \get_called_class();
        return new $class($data);
    }
    
    /**
     * Returns 8.
     * 
     * @return int
     */
    public function getLength()
    {
        return 8;
    }

    /**
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    protected function encodeData(EncoderInterface &$encoder, $data)
    {
        $encoded   = '';
        
        for ($i = 0; $i < 8; $i++) {
            $parity   = $i < 4 ? 0 : 2;
            $encoded .= self::$encodingTable[$data{$i}][$parity];
        }
        
        $encoder->addBinary('101', 1.3)
                ->addBinary(\substr($encoded, 0, 28))
                ->addBinary('01010', 1.3)
                ->addBinary(\substr($encoded, 28))
                ->addBinary('101', 1.3);
    }
}

/**
 * Class' exception
 */
class Ean8Exception extends Exception {}
