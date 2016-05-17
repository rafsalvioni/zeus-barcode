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
     * Stores the encoded string. Its a cache. Use getEncoded().
     * 
     * @var string
     */
    protected $encoded;

    /**
     * Checks if a data is compatible with barcode specification.
     * 
     * @return bool
     */
    abstract protected function checkData($data);
    
    /**
     * Encodes a data in a binary string, using only 1 or 0.
     * 
     * @return string
     */
    abstract protected function encodeData($data);

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
     * @return string
     */
    public function getEncoded()
    {
        if (empty($this->encoded)) {
            $this->encoded = $this->encodeData($this->data);
        }
        return $this->encoded;
    }
    
    /**
     * 
     * @param Renderer\RendererInterface $renderer
     * @return Renderer\RendererInterface
     */
    public function render(Renderer\RendererInterface $renderer)
    {
        $renderer->resetDraw();
        $encoded = $this->getEncoded();
        $renderer->setText($this->getPrintableData());
        
        while (\preg_match('/^(0+|1+)/', $encoded, $match)) {
            $width   = \strlen($match[1]);
            $encoded = \substr($encoded, $width);
            $renderer->drawBar($match[1]{0} == '1', $width);
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
