<?php

namespace Zeus\Barcode\Renderer;

use Zeus\Barcode\AbstractBarcode;
use Zeus\Barcode\Encoder\EncoderInterface;

/**
 * Null barcode used by renderers to avoid it use without barcode.
 *
 * @author Rafael M. Salvioni
 */
class NullBarcode extends AbstractBarcode
{
    /**
     * 
     */
    public function __construct($data = '')
    {
        parent::__construct('');
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return $data == '';
    }

    /**
     * 
     * @param Encoder\EncoderInterface $encoder
     * @param string $data
     */
    protected function encodeData(EncoderInterface &$encoder, $data) {}
    
    /**
     * 
     */
    protected function setDefaultOptions()
    {
        $this->setOption('barwidth', 0);
        $this->setOption('barheight', 0);
    }
}
