<?php

namespace Zeus\Barcode;

/**
 * Implements a EAN13 barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml
 */
class Ean13 extends AbstractBarcode
{
    /**
     * Product field
     * 
     */
    const PRODUCT = 7;

    /**
     * Parity table
     * 
     * 0 => Odd
     * 1 => Even
     * 
     * @var array
     */
    protected static $parityTable = [
        '0' => [0, 0, 0, 0, 0, 0],
        '1' => [0, 0, 1, 0, 1, 1],
        '2' => [0, 0, 1, 1, 0, 1],
        '3' => [0, 0, 1, 1, 1, 0],
        '4' => [0, 1, 0, 0, 1, 1],
        '5' => [0, 1, 1, 0, 0, 1],
        '6' => [0, 1, 1, 1, 0, 0],
        '7' => [0, 1, 0, 1, 0, 1],
        '8' => [0, 1, 0, 1, 1, 0],
        '9' => [0, 1, 1, 0, 1, 0],
    ];
    
    /**
     * Encoding table, with parity
     * 
     * [Odd, Even, Right]
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => ['0001101', '0100111', '1110010'],
        '1' => ['0011001', '0110011', '1100110'],
        '2' => ['0010011', '0011011', '1101100'],
        '3' => ['0111101', '0100001', '1000010'],
        '4' => ['0100011', '0011101', '1011100'],
        '5' => ['0110001', '0111001', '1001110'],
        '6' => ['0101111', '0000101', '1010000'],
        '7' => ['0111011', '0010001', '1000100'],
        '8' => ['0110111', '0001001', '1001000'],
        '9' => ['0001011', '0010111', '1110100'],
    ];
    
    /**
     * Padding zeros left on $data to complete the necessary length.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        $data = self::zeroLeftPadding($data, $hasChecksum ? 13 : 12);
        parent::__construct($data, $hasChecksum);
    }

    /**
     * Separates the first digit
     * 
     * @return string
     */
    public function getPrintableData()
    {
        $data = $this->getData(true);
        return $data{0} . ' ' . \substr($data, 1);
    }
    
    /**
     * Returns the product code.
     * 
     * @return string
     */
    public function getProductCode()
    {
        return $this->getDataPart(self::PRODUCT, 5);
    }
    
    /**
     * Creates a new instance with another product code.
     * 
     * @param string|int $code
     * @return Ean13
     */
    public function withProductCode($code)
    {
        $data = $this->withDataPart($code, self::PRODUCT, 5);
        return new self($data, false);
    }
    
    /**
     * Checks if barcode is compatible with UPC-A.
     * 
     * @return bool
     */
    public function isUpcACompatible()
    {
        return $this->data{0} == '0';
    }
    
    /**
     * Converts this barcode to a UPC-A barcode, if compatible. Otherwise,
     * a exception will be throw.
     * 
     * @return UpcA
     * @exception Ean13Exception
     */
    public function toUpcA()
    {
        if ($this->isUpcACompatible()) {
            return new UpcA(\substr($this->data, 1));
        }
        throw new Ean13Exception('Uncompatible UPC-A barcode!');
    }

    /**
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksum($data)
    {
        $data = \str_split($data);
        $sum  = 0;
        
        foreach ($data as $i => &$num) {
            $weight = ($i % 2) == 0 ? 1 : 3;
            $sum   += (int)$num * $weight;
        }
        
        $d = 10 - ($sum % 10);
        return $d == 10 ? 0 : $d;
    }

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        $len = 13;
        if (!$hasChecksum) {
            $len--;
        }
        return \preg_match("/^[0-9]{{$len}}$/", $data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $encoded   = '';
        $parityTab =& self::$parityTable[$data{0}];
        
        for ($i = 1; $i < 13; $i++) {
            $parity   = $i <= 6 ? $parityTab[$i - 1] : 2;
            $encoded .= self::$encodingTable[$data{$i}][$parity];
        }
        
        $encoded  = '101' .
                    \substr($encoded, 0, 42) .
                    '01010' .
                    \substr($encoded, 42) .
                    '101';
        
        return $encoded;
    }
}

/**
 * Ean13 exception
 */
class Ean13Exception extends Exception {}
