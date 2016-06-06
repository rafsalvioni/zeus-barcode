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
     * Render as PNG
     * 
     */
    public function render()
    {
        $this->checkStarted();
        \header('Content-Type: image/svg+xml');
        echo $this->resource->saveXML();
    }

    /**
     * 
     * @param array $point
     * @param number $width
     * @param number $height
     * @param int $color
     * @param bool $filled
     * @return self
     */
    public function drawRect(array $point, $width, $height, $color, $filled = true)
    {
        $this->checkStarted();
        
        $this->applyOffsets($point);
        
        $color   = self::formatColor($color);
        $attribs = [
            'x'      => $point[0],
            'y'      => $point[1],
            'width'  => $width,
            'height' => $height,
        ];
        if ($filled) {
            $attribs['fill'] = $color;
        }
        else {
            $attribs['fill-opacity'] = '0';
            $attribs['stroke'] = $color;
        }

        $this->appendRootElement('rect', $attribs);
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
        $this->checkStarted();
        
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
     * $resource can be a file path, string, \DOMDocument or \DOMElement object.
     * 
     * @param mixed $resource
     * @return self
     * @throws Exception
     */
    public function setResource($resource)
    {
        $isDOM = $resource instanceof \DOMDocument;
        if (!$isDOM) {
            $doc = new \DOMDocument();
            if (
                $resource instanceof \DOMElement && $doc->appendChild($resource) ||
                \file_exists($resource) && $doc->load($resource) ||
                \is_scalar($resource) && $doc->loadXML((string)$resource)
            ) {
                $resource =& $doc;
                $isDOM    = true;
            }
        }
        if (!$isDOM) {
            throw new Exception('SVG resource should be a XML file, string, DOMDocument or DOMElement object!');
        }
        if (!$resource->documentElement) {
            throw new Exception('SVG resource doesn\'t have a XML root element');
        }
        $this->resource = $resource;
        $this->setOption('merge', true);
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
        static $colors = [];
        if (!isset($colors[$color])) {
            $rgb = $color & 0xffffff;
            $colors[$color] = '#' . \str_pad(\dechex($rgb), 6, '0', \STR_PAD_LEFT);
        }
        return $colors[$color];
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
            $this->fillResource($width, $height);
        }
        $this->resizeResource($width, $height);
        
        $this->rootElement = $this->createElement('g', [
            'id' => \md5(\get_class($this->barcode) . $this->barcode->getData())
        ]);
        $this->resource->documentElement->appendChild($this->rootElement);
       
        $this->drawRect(
            [0, 0], $width, $height, $this->barcode->backColor, true
        );
        
        $this->resource->formatOutput = true;
    }
    
    /**
     * Resize resource.
     * 
     * @param number $width Additional width
     * @param number $height Additional height
     */
    protected function resizeResource($width, $height)
    {
        $svg  =& $this->resource->documentElement;
        
        $oldwidth  = $svg->getAttribute('width');
        $oldheight = $svg->getAttribute('height');
        
        $newwidth  = \max($oldwidth, $width + $this->offsetLeft);
        $newheight = \max($oldheight, $height + $this->offsetTop);
        
        if ($newwidth != $oldwidth || $newheight != $oldheight) {
            $svg->setAttribute('width', $newwidth);
            $svg->setAttribute('height', $newheight);
            $this->fillResource($newwidth, $newheight);
        }
    }
    
    /**
     * Fill the current resource with the define background color.
     * 
     * @param number $width
     * @param number $height
     */
    protected function fillResource($width, $height)
    {
        $svg   =& $this->resource->documentElement;
        $color = self::formatColor($this->options['backcolor']);
        $fill  = function() use ($width, $height, $color)
        {
            return $this->createElement('rect', [
                'x'      => 0, 'y' => 0,
                'width'  => $width,
                'height' => $height,
                'fill'   => $color
            ]);
        };
        if ($svg->firstChild) {
            $first =& $svg->firstChild;
            if ($first->tagName == 'rect' && $first->getAttribute('fill') == $color) {
                $first->setAttribute('width', $width);
                $first->setAttribute('height', $height);
            }
            else {
                $svg->insertBefore($fill(), $first);
            }
        }
        else {
            $svg->appendChild($fill());
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
            $element->appendChild(new \DOMText((string) $textContent));
        }
        return $element;
    }
}
