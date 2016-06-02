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
     * Create a instance using a ISSN number.
     * 
     * ISSN has a format like be XXXX-XXXX
     * 
     * @param string $issn
     * @return ISSN
     */
    public static function fromISSN($issn)
    {
        $issn = \preg_replace('/[^\d]/', '', $issn);
        $issn = self::SYSTEM . \substr($issn, 0, -1) . '00';
        return new self($issn, false);
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
        $issn = $this->getDataPart(3, 8);
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
            [$this->getTotalWidth() / 2, $this->options['border'] + 1],
            'ISSN ' . $this->getISSN(true),
            $this->options['forecolor'],
            $this->options['font'],
            $this->options['fontsize'],
            'center'
        );
        
        $this->options['barheight'] -= $textHeight;
    }
}
