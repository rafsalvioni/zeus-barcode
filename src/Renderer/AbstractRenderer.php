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
        $this->loadDefaultOptions();
    }
}
