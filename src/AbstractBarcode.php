<?php

namespace Zeus\Barcode;

/**
 * Abstract barcode.
 *
 * @author Rafael M. Salvioni
 */
abstract class AbstractBarcode implements BarcodeInterface
{
    /**
     * Encoded data
     * 
     * @var string
     */
    protected $encoded;
    /**
     * Barcode data, without checksum
     * 
     * @var string
     */
    protected $data;
    /**
     * Checksum
     * 
     * @var int
     */
    protected $checksum;
    
    /**
     * Calculates a barcode's checksum with a given data.
     * 
     * @param string $data
     * @return string
     */
    abstract protected function calcChecksum($data);

    /**
     * Validates a barcode data.
     * 
     * This method should validate only if data chars and length are valid.
     * Checksum will be validate later.
     * 
     * @param string $data
     * @param bool $hasChecksum Indicates if $data has a builtin checksum
     * @return bool
     */
    abstract protected function checkData($data, $hasChecksum = true);

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
     * Extract the checksum from a data.
     * 
     * @param string $data
     * @param string $cleanData Data without checksum
     * @return int 
     */
    protected function extractChecksum($data, &$cleanData)
    {
        $checksum  = \substr_remove($data, -1);
        $cleanData = $data;
        return $checksum;
    }
    
    /**
     * Insert a checksum on a data.
     * 
     * @param string $data
     * @param int $checksum
     * @return string
     */
    protected function insertChecksum($data, $checksum)
    {
        return $data . $checksum;
    }
    
    /**
     * $data will be validated in constructor. If $data don't have a checksum,
     * it will generated. Else, data's checksum will be validated too.
     * 
     * @param string $data
     * @param bool $hasChecksum Indicates if $data has a builtin checksum
     * @throws Exception If data or checksum is invalid
     */
    public function __construct($data, $hasChecksum = true)
    {
        if (!$this->checkData($data, $hasChecksum)) {
            throw new Exception('Invalid barcode data!');
        }
        if ($hasChecksum) {
            $checksum = $this->extractChecksum($data, $data);
            if ($checksum != $this->calcChecksum($data)) {
                throw new Exception('Invalid barcode checksum!');
            }
        }
        else {
            $checksum = $this->calcChecksum($data);
        }
        $this->data     = $data;
        $this->checksum = $checksum;
    }
    
    /**
     * 
     * @param bool $withChecksum
     * @return string
     */
    public function getData($withChecksum = false)
    {
        return $withChecksum ?
               $this->insertChecksum($this->data, $this->checksum) :
               $this->data;
    }
    
    /**
     * 
     * @return int
     */
    public function getChecksum()
    {
        return $this->checksum;
    }
    
    /**
     * 
     * @return string
     */
    final public function getEncoded()
    {
        if (empty($this->encoded)) {
            $this->encoded = $this->encodeData(
                $this->insertChecksum($this->data, $this->checksum)
            );
        }
        return $this->encoded;
    }
    
    /**
     * 
     * @return string
     */
    public function getPrintableData()
    {
        return $this->getData(true);
    }

    /**
     * On serialize, ignores "encoded" attribute.
     * 
     * @return string[]
     */
    public function __sleep()
    {
        return ['data', 'checksum'];
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
