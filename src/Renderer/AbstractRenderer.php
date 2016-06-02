<?php

namespace Zeus\Barcode\Renderer;

/**
 * Abstract renderer.
 *
 * @author Rafael M. Salvioni
 */
abstract class AbstractRenderer implements RendererInterface
{
    use RendererTrait;
    
    /**
     * Defines default options
     * 
     */
    public function __construct()
    {
        $this->options = [
            'offsettop'  => 0,
            'offsetleft' => 0,
            'merge'      => false,
            'backcolor'  => 0xffffff,
        ];
        $this->start(new NullBarcode());
    }
    
    /**
     * Apply offsets on a point.
     * 
     * @param array $point
     */
    protected function applyOffsets(array &$point)
    {
        if ($this->options['merge']) {
            $point[0] += $this->options['offsetleft'];
            $point[1] += $this->options['offsettop'];
        }
    }
}
