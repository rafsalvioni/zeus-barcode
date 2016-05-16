<?php

namespace Zeus\Barcode;

/**
 * Simple abstract barcode that implements BarcodeInterface and
 * BarcodeTrait.
 *
 * @author Rafael M. Salvioni
 */
abstract class AbstractBarcode implements BarcodeInterface
{
    use BarcodeTrait;
    
    /**
     * Encoded data
     * 
     * @var string
     */
    protected $encoded;
    
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

    /**
     * Validates a barcode data.
     * 
     * This method should validate only if data chars and length are valid.
     * 
     * @param string $data

     * @return bool
     */
    abstract protected function checkData($data);

    /**
     * Encodes a data.
     * 
     * Should return a string formed by 0 and 1 only.
     * 
     * @param string $data
     * @return string
     */
    abstract protected function encodeData($data);
    
    /**
     * $data will be validated in constructor.
     * 
     * @param string $data
     * @throws Exception If data is invalid
     */
    public function __construct($data)
    {
        if (!$this->checkData($data)) {
            throw $this->createException('Invalid "%class%" barcode data chars or length!');
        }
        $this->data = $data;
    }
    
    /**
     * 
     * @return string
     */
    final public function getEncoded()
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
}
