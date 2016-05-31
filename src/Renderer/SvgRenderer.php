<?php

namespace Zeus\Barcode\Renderer;

use \DOMDocument;
use \DOMText;

/**
 * Renderer to draw barcodes as XML-SVG markup.
 *
 * @author Rafael M. Salvioni
 */
class SvgRenderer extends AbstractRenderer
{
    protected $rootElement;
    
    /**
     * Render as PNG
     * 
     */
    public function render()
    {
        \header('Content-Type: image/svg+xml');
        echo $this->resource->saveXML();
    }

    /**
     * 
     * @param array $points
     * @param int $color
     * @param bool $filled
     */
    public function drawPolygon(array $points, $color, $filled = true)
    {
        $ps = [];
        foreach ($points as &$point) {
            $this->applyOffsets($point);
            $ps = \array_merge($ps, \array_values($point));
        }
        
        $n       = \count($points);
        $attribs = [
            'style'  => 'stroke:' . self::formatColor($color),
        ];
        if ($filled) {
            $attribs['style'] .= ';fill:' . self::formatColor($color);
        }
        $attribs['style'] .= ';stroke-width:1';
        
        if ($n > 3) {
            $attribs['points'] = \implode(',', $ps);
            $this->appendRootElement('polygon', $attribs);
        }
        else if ($n == 2) {
            $attribs['x1'] =& $points[0]['x'];
            $attribs['y1'] =& $points[0]['y'];
            $attribs['x2'] =& $points[1]['x'];
            $attribs['y2'] =& $points[1]['y'];
            $this->appendRootElement('line', $attribs);
        }
        else if ($n == 1) {
            $attribs['x1'] =& $points[0]['x'];
            $attribs['y1'] =& $points[0]['y'];
            $attribs['x2'] =& $points[0]['x'];
            $attribs['y2'] =& $points[0]['y'];
            $this->appendRootElement('line', $attribs);
        }
    }

    /**
     * 
     * @param array $point
     * @param string $text
     * @param int $color
     * @param string $font
     * @param int $fontSize
     */
    public function drawText(array $point, $text, $color, $font, $fontSize)
    {
    }
    
    /**
     * 
     * @return int
     */
    public function getTextHeight()
    {
    }

    /**
     * 
     * @param string $text
     * @return int
     */
    public function getTextWidth($text = null)
    {
    }

    /**
     * Allows use a another XML document where barcode will be drawed.
     * 
     * $resource can be a file path, string or DOMDocument object.
     * 
     * @param mixed $resource
     * @return SvgRenderer
     */
    public function setResource($resource)
    {
        if (\file_exists($resource)) {
            $resource = \DOMDocument::load($resource);
        }
        else if (!\is_object($resource)) {
            $resource = \DOMDocument::loadXML((string)$resource);
        }
        if ($resource instanceof DOMDocument) {
            $this->external = $resource;
        }
        else {
            $this->external = null;
        }
        return $this;
    }
    
    protected static function formatColor($color)
    {
        $rgb = self::colorToRgb($color);
        return 'rgb(' . \implode(',', $rgb) . ')';
    }

    /**
     * 
     */
    protected function initResource()
    {
        if (!$this->external) {
            $width  = $this->getTotalWidth();
            $height = $this->getTotalHeight();
            
            $this->resource = new DOMDocument('1.0', 'utf-8');
            $this->resource->formatOutput = true;
            $this->rootElement = $this->resource->createElement('svg');
            $this->resource->appendChild($this->rootElement);
            $this->rootElement->setAttribute('xmlns', "http://www.w3.org/2000/svg");
            $this->rootElement->setAttribute('version', '1.1');
            $this->rootElement->setAttribute('width', $width);
            $this->rootElement->setAttribute('height', $height);
            $this->rootElement->setAttribute('fill', self::formatColor($this->barcode->backColor));
        }
        else {
            $this->resource =& $this->external;
        }
    }
    
    /**
     * Append a new DOMElement to the root element
     *
     * @param string $tagName
     * @param array $attributes
     * @param string $textContent
     */
    protected function appendRootElement($tagName, $attributes = [], $textContent = null)
    {
        $newElement = $this->createElement($tagName, $attributes, $textContent);
        $this->rootElement->appendChild($newElement);
    }
    
    /**
     * Create DOMElement
     *
     * @param string $tagName
     * @param array $attributes
     * @param string $textContent
     * @return DOMElement
     */
    protected function createElement($tagName, $attributes = [], $textContent = null)
    {
        $element = $this->resource->createElement($tagName);
        foreach ($attributes as $k => $v) {
            $element->setAttribute($k, $v);
        }
        if ($textContent !== null) {
            $element->appendChild(new DOMText((string) $textContent));
        }
        return $element;
    }
}
