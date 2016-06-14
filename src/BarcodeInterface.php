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
     * Returns the Bar set of current barcode.
     * 
     * @return Encoder\EncoderInterface
     */
    public function getEncoded();
    
    /**
     * Returns the printable data of barcode.
     * 
     * @return string
     */
    public function getDataToDisplay();
    
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
     * @return string
     */
    public function withDataPart($value, $start, $length);
    
    /**
     * Sets a draw option.
     * 
     * @param string $option
     * @param value $value
     * @return self
     */
    public function setOption($option, $value);
    
    /**
     * Returns a draw option.
     * 
     * @param string $option
     * @return mixed
     */
    public function getOption($option);
    
    /**
     * Returns the barcode's draw options.
     * 
     * @return array
     */
    public function getOptions();
    
    /**
     * Returns the width of barcode area.
     * 
     * @return number
     */
    public function getWidth();
    
    /**
     * Returns the height of barcode area.
     * 
     * @return number
     */
    public function getHeight();
    
    /**
     * Returns the total barcode width, including barcode area, quietzones,
     * border etc.
     * 
     * @return int
     */
    public function getTotalWidth();
    
    /**
     * Returns the total barcode height, including barcode area, border, text etc.
     * 
     * @return int
     */
    public function getTotalHeight();
    
    /**
     * Applies a multiplier factor do resize all dimension options
     * proportionallity.
     * 
     * @param number $factor
     * @return self
     */
    public function scale($factor);

    /**
     * Draw the barcode to a renderer object.
     * 
     * Returns the own renderer.
     * 
     * @param Renderer\RendererInterface $renderer
     * @return Renderer\RendererInterface
     */
    public function draw(Renderer\RendererInterface $renderer);
}
