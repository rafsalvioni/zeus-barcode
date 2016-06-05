<?php

namespace Zeus\Barcode\Upc;

use Zeus\Barcode\Renderer\RendererInterface;

/**
 * Implements a EAN13-ISSN barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml
 */
class ISSN extends Ean13
{
    /**
     * EAN-13 ISSN system digits
     * 
     */
    const SYSTEM = '977';
    /**
     * ISSN number
     * 
     * @var string
     */
    protected $issn;

    /**
     * Create a instance using a ISSN number.
     * 
     * ISSN has a format like be XXXX-XXXX. Zero left padding will be
     * applied if necessary.
     * 
     * If ISSN number is invalid, a exception will be thrown.
     * 
     * @param string $issn
     * @return ISSN
     * @throws ISSNException
     */
    public static function fromISSN($issn)
    {
        $issn  = \preg_replace('/[^\d]/', '', $issn);
        $check = \substr_remove($issn, -1);
        if (!empty($issn)) {
            $issn = self::zeroLeftPadding($issn, 7);
            $me   = new self(self::SYSTEM . $issn . '00', false);
            if ($me->issn == $issn . \strtoupper($check)) {
                return $me;
            }
        }
        throw new ISSNException('Invalid ISSN checksum number!');
    }
    
    /**
     * Calculates ISSN.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        parent::__construct($data, $hasChecksum);
        $this->issn  = $this->getDataPart(3, 7);
        $this->issn .= self::calcISSNCheck($this->issn);
    }

    /**
     * ISSN barcode draws two texts. So, we can adjust height...
     * 
     * @return int
     */
    public function getTotalHeight()
    {
        $height = parent::getTotalHeight();
        if ($this->options['showtext']) {
            $height += $this->getTextHeight();
        }
        return $height;
    }
    
    /**
     * Returns the ISSN number.
     * 
     * @param bool $mask Mask number?
     * @return string
     */
    public function getISSN($mask = false)
    {
        $issn = $this->issn;
        if ($mask) {
            $issn = \mask('####-####', $issn);
        }
        return $issn;
    }

    /**
     * ISSN's barcodes is a EAN-13 beggining with 977.
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        if (\strpos($data, self::SYSTEM) === 0) {
            return parent::checkData($data);
        }
        return false;
    }
    
    /**
     * Force barcode to be drawed with a top offset.
     * 
     * @param RendererInterface $renderer
     */
    protected function drawBarcode(RendererInterface &$renderer)
    {
        $this->options['textposition'] = 'top';
        parent::drawBarcode($renderer);
        $this->options['textposition'] = 'bottom';
    }
    
    /**
     * Draw the ISSN text on barcode.
     * 
     * @param RendererInterface $renderer
     */
    protected function drawText(RendererInterface &$renderer)
    {
        $textHeight = $this->getTextHeight();
        $this->options['barheight'] += $textHeight;
        parent::drawText($renderer);
        
        $renderer->drawText(
            [$this->getTextX(), $this->getContentOffsetTop() + 1],
            'ISSN ' . $this->getISSN(true),
            $this->options['forecolor'],
            $this->options['font'],
            $this->options['fontsize'],
            'center'
        );
        
        $this->options['barheight'] -= $textHeight;
    }
    
    /**
     * Calc ISSN checksum.
     * 
     * @param string $issn
     * @return string
     */
    protected static function calcISSNCheck($issn)
    {
        $issn  = \str_split($issn);
        $sum   = self::sumCrescentWeight($issn, 2);
        $check = 11 - ($sum % 11);
        return $check == 10 ? 'X' : $check;
    }
}

/**
 * Class' exception
 * 
 */
class ISSNException extends Exception {}
