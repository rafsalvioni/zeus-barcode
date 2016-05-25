<?php

namespace Zeus\Barcode;

/**
 * Abstract barcode.
 *
 * @author Rafael M. Salvioni
 */
abstract class AbstractBarcode implements BarcodeInterface
{
    use BarcodeTrait;
    
    /**
     * Checks if a data is compatible with barcode specification.
     * 
     * @return bool
     */
    abstract protected function checkData($data);
    
    /**
     * 
     * @param string $data
     * @throws Exception
     */
    public function __construct($data)
    {
        if ($this instanceof FixedLengthInterface) {
            $data = self::zeroLeftPadding($data, $this->getLength());
        }
        if (!$this->checkData($data)) {
            throw $this->createException('Invalid "%class%" barcode data chars or length!');
        }
        $this->data = $data;
    }

    /**
     * 
     * @param Renderer\RendererInterface $renderer
     * @param array $options
     * @return Renderer\RendererInterface
     */
    public function render(Renderer\RendererInterface $renderer, array $options = [])
    {
        $this->getEncoded();
        
        $text     = \array_get($options, 'text', false);
        $textSize = \array_get($options, 'font-size', 16);
        $ydiff    = 0;
        
        if ($text) {
            $height = 1.5;
            if ($text == 'top') {
                $ydiff = 0.5;
            }
        }
        else {
            $height = 1;
        }
        
        $renderer->startResource($this->encoded->getWidthFactor(), $height);
        
        foreach ($this->encoded as $bar) {
            $renderer->drawBar($bar->b, $bar->w, $bar->h, $bar->y + $ydiff);
        }
        
        if ($text == 'top') {
            $renderer->writeText($this->getPrintableData(), $textSize);
        }
        else {
            $renderer->writeText($this->getPrintableData(), $textSize, 1.1);
        }
        
        return $renderer;
    }
    
    /**
     * Serializes only $data property.
     * 
     * @return string[]
     */
    public function __sleep()
    {
        return ['data'];
    }
    
    /**
     * Create a barcode exception using current class. The class name formatted
     * will be put on '%class%' mark of $message.
     * 
     * @param string $message
     * @return Exception
     */
    protected function createException($message)
    {
        $class = \get_class($this);
        $class = \str_replace(__NAMESPACE__ . '\\', '', $class);
        return new Exception(\str_replace('%class%', $class, $message));
    }
}
