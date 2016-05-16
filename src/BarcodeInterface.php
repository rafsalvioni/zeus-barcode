<?php

namespace Zeus\Barcode;

/**
 * Identifies a generic barcode.
 * 
 * @author Rafael M. Salvioni
 */
interface BarcodeInterface
{
    /**
     * Constructor should be check if $data is compatible with barcode
     * especification and throw a exception if not.
     * 
     * @param string $data
     */
    public function __construct($data);

    /**
     * Returns the barcode data.
     * 
     * @return string
     */
    public function getData();
    
    /**
     * Returns the encoded barcode data.
     * 
     * The string returned should have only 0 and 1.
     * 
     * @return string
     */
    public function getEncoded();
    
    /**
     * Returns the printable data of barcode.
     * 
     * @return string
     */
    public function getPrintableData();
    
    /**
     * Returns a part of barcode data.
     * 
     * @param int $start Start position
     * @param int $length Part length
     * @return string
     */
    public function getDataPart($start, $length);
    
    /**
     * Gets the barcode data, replaces a part of it for another and returns
     * the result.
     * 
     * If $part is less than $length, it will be padding qith zeros left. If
     * greater, a exception should be throwed.
     * 
     * @param string $value Replacement
     * @param int $start Start position
     * @param int $length Part data length
     */
    public function withDataPart($value, $start, $length);
    
    /**
     * Render the barcode to a renderer object.
     * 
     * Returns the own renderer.
     * 
     * @param Renderer\RendererInterface $renderer
     * @return Renderer\RendererInterface
     */
    public function render(Renderer\RendererInterface $renderer);
}
