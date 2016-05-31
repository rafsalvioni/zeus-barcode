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
            'source'     => null,
            'offsettop'  => 0,
            'offsetleft' => 0,
        ];
    }
    
    /**
     * Apply offsets on a point if has a source defined.
     * 
     * @param array $point
     */
    protected function applyOffsets(array &$point)
    {
        if ($this->options['source']) {
            $point['x'] += $this->options['offsetleft'];
            $point['y'] += $this->options['offsettop'];
        }
    }
}
