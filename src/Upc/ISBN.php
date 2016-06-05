<?php

namespace Zeus\Barcode\Upc;

use Zeus\Barcode\Renderer\RendererInterface;

/**
 * Implements a EAN13-ISBN barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/ean13.phtml
 */
class ISBN extends Ean13
{
    /**
     * EAN-13 ISBN system digits
     * 
     */
    const SYSTEM = '978';
    
    /**
     * Create a barcode instance from a ISBN code.
     * 
     * @param string $isbn
     * @return self
     */
    public static function fromISBN($isbn)
    {
        $isbn = \preg_replace('/[^\d]/', '', $isbn);
        $isbn = \substr($isbn, 0, -1);
        if (\strlen($isbn) == 9) {
            return new self(self::SYSTEM . $isbn, false);
        }
        else {
            return new self($isbn, true);
        }
    }
    
    /**
     * Return the ISBN number, masked or not.
     * 
     * @param bool $mask Mask number?
     * @return string
     */
    public function getISBN($mask = false)
    {
        $isbn = $this->getData();
        if ($mask) {
            $isbn = \mask('###-##-###-####-#', $isbn);
        }
        return $isbn;
    }

    /**
     * ISBN barcode draws two texts. So, we can adjust height...
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
     * ISBN's barcodes is a EAN-13 beggining with 978.
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
     * Draw the ISBN text on barcode.
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
            'ISBN ' . $this->getISBN(true),
            $this->options['forecolor'],
            $this->options['font'],
            $this->options['fontsize'],
            'center'
        );
        
        $this->options['barheight'] -= $textHeight;
    }
}
