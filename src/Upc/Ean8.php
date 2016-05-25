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
     * Stores the system code length
     * 
     * @var int
     */
    protected $systemLength;

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        parent::__construct($data, $hasChecksum);
        $this->systemLength = \strlen(self::getSystemDigits($this->data));
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
     * Returns the Ean13 system code.
     * 
     * @return string
     */
    public function getSystemCode()
    {
        return $this->getDataPart(0, $this->systemLength);
    }
    
    /**
     * Creates a new instance using another system code.
     * 
     * @param string|int $code Code, 1-3 digits
     * @return Ean13
     * @throws Ean13Exception If $code can't be changed
     */
    public function withSystemCode($code)
    {
        $item = $this->getItemCode();
        $n    = \strlen($code);
        
        if ($n == $this->systemLength) {
            $len = $n;
        }
        else if ($n > $this->systemLength && $item{0} == '0') {
            $len = 3;
        }
        else if ($n < $this->systemLength) {
            $len   = 3;
            $code .= '0';
        }
        else {
            throw new Ean8Exception("Unable to define \"$code\" as system code");
        }
        $data = $this->withDataPart($code, 0, $len);
        return new self($data, false);
    }
    
    /**
     * Returns the manufacturer code.
     * 
     * @return string
     */
    public function getItemCode()
    {
        $len = $this->systemLength == 2 ? 5 : 4;
        return $this->getDataPart($this->systemLength, $len);
    }
    
    /**
     * Creates a new instance using another system code.
     * 
     * @param string|int $code Code, 1-5 digits (depending of system code)
     * @return Ean13
     */
    public function withItemCode($code)
    {
        $len  = $this->systemLength == 2 ? 5 : 4;
        $data = $this->withDataPart($code, $this->systemLength, $len);
        return new self($data, false);
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
        
        $encoder->addBinary('101')
                ->addBinary(\substr($encoded, 0, 28))
                ->addBinary('01010')
                ->addBinary(\substr($encoded, 28))
                ->addBinary('101');
    }
}

/**
 * Class' exception
 * 
 */
class Ean8Exception extends Exception {}