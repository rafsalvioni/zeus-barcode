<?php

namespace Zeus\Barcode\Renderer;

/**
 * Renderer to draw barcodes as images.
 *
 * @author Rafael M. Salvioni
 */
class ImageRenderer extends AbstractRenderer
{
    /**
     * Stores the registered fonts. Its a cache. Use resolveFont()
     * 
     * @var array
     */
    private static $fonts = [];    
    /**
     * Stores the allocated colors. Its a cache. Use getColorId()
     * 
     * @var array
     */
    private $colors = [];
    
    /**
     * Render as PNG
     * 
     */
    public function render()
    {
        $this->checkStarted();
        \header('Content-Type: image/png');
        \imagepng($this->resource);
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
        $this->checkStarted();
        
        $color  = $this->getColorId($color);
        $points = \array_values(\array_unique($points, \SORT_REGULAR));
        $ps     = [];
        $n      = \count($points);
        
        foreach ($points as &$point) {
            $this->applyOffsets($point);
            $ps = \array_merge($ps, \array_values($point));
        }
            
        if ($n == 4) {
            $f = $filled ? '\\imagefilledpolygon' : '\\imagepolygon';
            $f($this->resource, $ps, \count($points), $color);
        }
        else if ($n == 2) {
            \imageline($this->resource, $points[0][0], $points[0][1], $points[1][0], $points[1][1], $color);
        }
        else if ($n == 1) {
            \imagesetpixel($this->resource, $points[0][0], $points[0][1], $color);
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
        $this->checkStarted();
        
        $color     = $this->getColorId($color);
        $font      = $this->resolveFont($font, $fontSize);
        $width     = $this->barcode->getTotalWidth();
        $offset    = $this->barcode->border + $this->barcode->quietZone;
        $textWidth = $this->getTextWidth($font, $fontSize, $text);
        
        switch ($align) {
            case 'center':
                $point[0] -= \ceil($textWidth / 2);
                break;
            case 'right':
                $point[0] -= $textWidth;
                break;
        }
        
        $this->applyOffsets($point);
        
        if (\is_int($font)) {
            \imagestring($this->resource, $font, $point[0], $point[1], $text, $color);
        }
        else {
            $point[1] += $fontSize;
            \imagettftext($this->resource, $fontSize, 0, $point[0], $point[1], $color, $font, $text);
        }
        return $this;
    }
    
    /**
     * Allows use a another image where barcode will be drawed.
     * 
     * $source can be a GD resource or a image file path.
     * 
     * @param mixed $resource
     * @return ImageRenderer
     */
    public function setResource($resource)
    {
        if (\is_resource($resource) && \strpos('gd', \get_resource_type($resource)) !== false) {
            $this->resource = $resource;
        }
        else if (\file_exists($resource)) {
            $this->resource = \imagecreatefromstring(\file_get_contents($resource));
        }
        else {
            throw new Exception('Invalid image resource!');
        }
        $this->options['merge'] = true;
        return $this;
    }

    /**
     * 
     */
    public function initResource()
    {
        $width  = $this->barcode->getTotalWidth();
        $height = $this->barcode->getTotalHeight();
        
        if (!$this->resource || !$this->options['merge']) {
            $this->resource = \imagecreatetruecolor($width, $height);
            $this->colors   = [];
            //\imagefill($this->resource, 0, 0, $this->getColorId($this->options['backcolor']));
        }
        if ($this->options['merge']) {
            $this->resizeResource($width, $height);
        }
        
        // Fill barcode's background
        $point = [
            0 => 0,
            1 => 0,
        ];
        $this->applyOffsets($point);
        
        $color = $this->getColorId($this->barcode->backColor);
        \imagefilledrectangle(
            $this->resource,
            $point[0],
            $point[1],
            $width + $point[0] - 1,
            $height + $point[1] - 1,
            $color
        );
    }
    
    /**
     * Resize the resource.
     * 
     * @param number $width
     * @param number $height
     */
    protected function resizeResource($width, $height)
    {
        $width  += $this->offsetLeft;
        $height += $this->offsetTop;

        $oldwidth  = \imagesx($this->resource);
        $oldheight = \imagesy($this->resource);
        
        $newwidth  = \max($width, $oldwidth);
        $newheight = \max($height, $oldheight);
        
        if ($newwidth != $oldwidth || $newheight != $oldheight) {
            $resource = \imagecreatetruecolor($newwidth, $newheight);
            \imagecopymerge($resource, $this->resource, 0, 0, 0, 0, $oldwidth, $oldheight, 100);
            \imagefill($resource, 0, 0, $this->getColorId($this->options['backcolor']));
            \imagedestroy($this->resource);
            $this->resource = $resource;
        }
    }

    /**
     * Returns the color index to be used. If a color is not allocated, it will be.
     * 
     * @param int $color
     * @return int
     */
    protected function getColorId($color)
    {
        if (isset($this->colors[$color])) {
            return $this->colors[$color];
        }
        
        list($r, $g, $b) = self::colorToRgb($color);
        
        $i = \imagecolorexact($this->resource, $r, $g, $b);
        if ($i == -1) {
            $i = \imagecolorallocate($this->resource, $r, $g, $b);
        }
        
        $this->colors[$color] = $i;
        return $i;
    }
    
    /**
     * Returns the font to be used by GD.
     * 
     * If a integer, use a internal (or loaded) GD font. If a string,
     * use a TTF font.
     * 
     * @param string $font
     * @param int $fontSize
     * @return int
     */
    protected function resolveFont($font, $fontSize)
    {
        if (!$font) {
            if (\in_array($fontSize, self::$fonts)) {
                return $fontSize;
            }
            return $fontSize % 6;
        }
        else if (\substr($font, -4) == '.ttf') {
            return $font;
        }
        else if (\substr($font, -4) == '.gdf') {
            $font = \realpath($font);
            $idx  = \crc32($font);
            if (!isset(self::$fonts[$idx])) {
                self::$fonts[$idx] = \imageloadfont($font);
            }
            return self::$fonts[$idx];
        }
        else {
            return 0;
        }
    }
    
    /**
     * Returns the width of a text.
     * 
     * @param string|int $font
     * @param int $fontSize
     * @param string $text
     * @return number
     */
    protected function getTextWidth($font, $fontSize, $text)
    {
        if (\is_int($font)) {
            return \imagefontwidth($font) * \strlen($text);
        }
        else {
            $box = \imagettfbbox($fontSize, 0, $font, $text);
            return \abs($box[2] - $box[0]);
        }
    }
}
