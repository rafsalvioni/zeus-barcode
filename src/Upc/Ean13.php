<?php

namespace Zeus\Barcode\Upc;

use Zeus\Barcode\AbstractChecksumBarcode,
    Zeus\Barcode\FixedLengthInterface,
    Zeus\Barcode\Encoder\EncoderInterface,
    Zeus\Barcode\Renderer\RendererInterface;

/**
 * Implements a EAN13 barcode standard.
 * 
 * Supports 13 numeric chars and the last digit it's the checksum.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml
 */
class Ean13 extends AbstractChecksumBarcode implements FixedLengthInterface
{
    use EanHelperTrait;
    
    /**
     * Product field
     * 
     */
    const PRODUCT = 7;
    /**
     * System digits field
     * 
     */
    const SYSTEM  = 0;
    /**
     * Stores the lenght of system digits
     * 
     * @var int
     */
    protected $systemLength;

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
     * Create a instance using given parameters.
     * 
     * @param string|int $systemCode
     * @param string|int $mfgCode
     * @param string|int $prodCode
     * @return Ean13
     */
    public static function builder($systemCode, $mfgCode, $prodCode)
    {
        $me = new self('', false);
        return $me->withSystemCode($systemCode)
                  ->withManufacurerCode($mfgCode)
                  ->withProductCode($prodCode);
    }

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
     * Returns 13.
     * 
     * @return int
     */
    public function getLength()
    {
        return 13;
    }
    
    /**
     * Returns the Ean13 system code.
     * 
     * @return string
     */
    public function getSystemCode()
    {
        return $this->getDataPart(self::SYSTEM, $this->systemLength);
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
        $mfg = $this->getManufacturerCode();
        $n   = \strlen($code);
        
        if ($n == $this->systemLength) {
            $len = $n;
        }
        else if ($n > $this->systemLength && $mfg[0] == '0') {
            $len = 3;
        }
        else if ($n < $this->systemLength) {
            $len   = 3;
            $code .= '0';
        }
        else {
            throw new Ean13Exception("Unable to define \"$code\" as system code");
        }
        $data = $this->withDataPart($code, self::SYSTEM, $len);
        return new self($data, false);
    }
    
    /**
     * Returns the manufacturer code.
     * 
     * @return string
     */
    public function getManufacturerCode()
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
    public function withManufacurerCode($code)
    {
        $len  = $this->systemLength == 2 ? 5 : 4;
        $data = $this->withDataPart($code, $this->systemLength, $len);
        return new self($data, false);
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
     * @param string|int $code Code, 1-5 digits
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
    public function isUpcaCompatible()
    {
        return $this->data[0] == '0';
    }
    
    /**
     * Converts this barcode to a UPC-A barcode, if compatible. Otherwise,
     * a exception will be throw.
     * 
     * @return Upca
     * @exception Ean13Exception
     */
    public function toUpca()
    {
        if ($this->isUpcaCompatible()) {
            return new Upca(\substr($this->data, 1));
        }
        throw new Ean13Exception('Uncompatible UPC-A barcode!');
    }
    
    /**
     * To UPC-E, text position is always "bottom".
     * 
     * @param string $value
     * @return self
     */
    public function setTextPosition($value)
    {
        return $this->checkAndSetOption('textposition', 'bottom');
    }
    
    /**
     * 
     * @return int
     */
    public function getTotalHeight()
    {
        $height = parent::getTotalHeight();
        if ($this->options['showtext']) {
            $height -= (int)\ceil($this->options['barheight'] * 0.2);
        }
        return $height;
    }
    
    /**
     * 
     * @return number
     */
    protected function getTextY()
    {
        return $this->getContentOffsetTop() + $this->options['barheight'] + 2;
    }

   /**
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    protected function encodeData(EncoderInterface &$encoder, $data)
    {
        $encoded   = '';
        $parityTab =& self::$parityTable[$data[0]];
        
        for ($i = 1; $i < 13; $i++) {
            $parity   = $i <= 6 ? $parityTab[$i - 1] : 2;
            $encoded .= self::$encodingTable[$data[$i]][$parity];
        }
        
        $barHeight = $this->showText ? 1.2 : 1;
        
        $encoder->addBinary('101', $barHeight)
                ->addBinary(\substr($encoded, 0, 42))
                ->addBinary('01010', $barHeight)
                ->addBinary(\substr($encoded, 42))
                ->addBinary('101', $barHeight);
    }
    
    /**
     * Draw a text specifically to Ean13.
     * 
     * @param RendererInterface $renderer
     */
    protected function drawText(RendererInterface &$renderer)
    {
        $text = $this->getData();
        $text = [$text[0], \substr($text, 1, 6), \substr($text, 7)];
        
        $foreColor =& $this->options['forecolor'];
        $font      =& $this->options['font'];
        $fontSize  =& $this->options['fontsize'];
        $barWidth  =& $this->options['barwidth'];
        
        $offX = $this->getContentOffsetLeft() + 1;
        $y    = $this->getTextY();

        $x = $offX - 3;
        $renderer->drawText([$x, $y], $text[0], $foreColor, $font, $fontSize, 'right');
        $x += 3 + $barWidth * 24;
        $renderer->drawText([$x, $y], $text[1], $foreColor, $font, $fontSize, 'center');
        $x += $barWidth * 46;
        $renderer->drawText([$x, $y], $text[2], $foreColor, $font, $fontSize, 'center');
    }
}

/**
 * Ean13 exception
 */
class Ean13Exception extends Exception {}
