<?php

namespace Zeus\Barcode\Renderer;

/**
 * Renderer to draw barcodes as XML-SVG markup.
 *
 * @author Rafael M. Salvioni
 */
class SvgRenderer extends AbstractRenderer
{
    /**
     * SVG element
     * 
     * @var DOMElement
     */
    protected $rootElement;
    /**
     * Group counter
     * 
     * @var int
     */
    protected $groupCounter = 0;

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
     * @return self
     */
    public function drawRect(array $points, $color, $filled = true)
    {
        $n  = \count($points);
        foreach ($points as &$point) {
            $this->applyOffsets($point);
        }
        
        $color = self::formatColor($color);
        if ($filled) {
            $attribs['fill'] = $color;
        }
        else {
            $attribs['fill-opacity'] = '0';
            $attribs['stroke'] = $color;
        }

        if ($n == 4) {
            $attribs['x']      = $points[0][0];
            $attribs['y']      = $points[0][1];
            $attribs['width']  = $points[1][0] - $points[0][0] + 1;
            $attribs['height'] = $points[2][1] - $points[1][1] + 1;
            $this->appendRootElement('rect', $attribs);
        }
        return $this;
    }

    /**
     * 
     * @param array $point
     * @param string $text
     * @param int $color
     * @param string $font
     * @param int $fontSize
     * @param string $align
     * @return self
     */
    public function drawText(
        array $point, $text, $color, $font, $fontSize, $align = null
    ) {
        $this->applyOffsets($point);
        $attribs = [
            'x'    => $point[0],
            'y'    => $point[1] + $fontSize,
            'fill' => self::formatColor($color),
        ];
        switch ($align) {
            case 'center':
                $attribs['text-anchor'] = 'middle';
                break;
            case 'right':
                $attribs['text-anchor'] = 'end';
                break;
            default:
                $attribs['text-anchor'] = 'start';
                break;
        }
        if ($font) {
            $font = \preg_replace('/\.[a-z\d]{1,4}$/i', '', \basename($font));
            $attribs['font-family'] = $font;
        }
        if ($fontSize) {
            $attribs['font-size'] = "{$fontSize}pt";
        }
        $this->appendRootElement('text', $attribs, $text);
        return $this;
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
        if (!($resource instanceof \DOMDocument)) {
            $doc = new \DOMDocument();
            if (
                $resource instanceof \DOMElement && $doc->appendChild($resource) ||
                \file_exists($resource) && $doc->load($resource) ||
                \is_scalar($resource) && $doc->loadXML((string)$resource)
            ) {
                $resource =& $doc;
            }
        }
        if ($resource instanceof \DOMDocument) {
            $this->resource = $resource;
        }
        else {
            throw new Exception('SVG resource should be a XML file, string or \\DOMDocument object');
        }
        $this->options['merge'] = true;
        return $this;
    }
    
    /**
     * Convert a integer color to a RGB format used by SVG.
     * 
     * @param int $color
     * @return string
     */
    protected static function formatColor($color)
    {
        $rgb = $color & 0xffffff;
        return '#' . \str_pad(\dechex($rgb), 6, '0', \STR_PAD_LEFT);
    }

    /**
     * 
     */
    protected function initResource()
    {
        $width  = $this->barcode->getTotalWidth();
        $height = $this->barcode->getTotalHeight();

        if (!$this->resource || !$this->options['merge']) {
            $this->resource = new \DOMDocument('1.0', 'utf-8');
            $svg = $this->createElement('svg', [
                'width'   => $width,
                'height'  => $height,
                'version' => '1.1',
                'xmlns'   => 'http://www.w3.org/2000/svg',
            ]);
            $this->resource->appendChild($svg);
        }
        else if ($this->options['merge']) {
            $this->resizeResource($width, $height);
        }
        
        $this->rootElement = $this->createElement('g', [
            'id' => $this->groupCounter++
        ]);
        $this->resource->documentElement->appendChild($this->rootElement);
       
        $this->appendRootElement('rect', [
            'x'      => $this->offsetLeft,
            'y'      => $this->offsetTop,
            'width'  => $width,
            'height' => $height,
            'fill'   => self::formatColor($this->barcode->backColor)
        ]);
        
        $this->resource->formatOutput = true;
    }
    
    protected function resizeResource($width, $height)
    {
        $svg =& $this->resource->documentElement;
        
        $oldwidth  = $svg->getAttribute('width');
        $oldheight = $svg->getAttribute('height');
        
        $newwidth  = \max($oldwidth, $width + $this->offsetLeft);
        $newheight = \max($oldheight, $height + $this->offsetTop);
        
        $svg->setAttribute('width', $newwidth);
        $svg->setAttribute('height', $newheight);
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
            $element->appendChild(new \DOMText((string) $textContent));
        }
        return $element;
    }
}
