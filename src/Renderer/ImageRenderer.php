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
        \header('Content-Type: image/png');
        \imagepng($this->resource);
    }

    /**
     * 
     * @param array $points
     * @param int $color
     * @param bool $filled
     */
    public function drawPolygon(array $points, $color, $filled = true)
    {
        $color = $this->getColorId($color);
        $ps    = [];
        $n     = \count($points);
        
        foreach ($points as &$point) {
            $this->applyOffsets($point);
            $ps = \array_merge($ps, \array_values($point));
        }
            
        if ($n > 3) {
            $f = $filled ? '\\imagefilledpolygon' : '\\imagepolygon';
            $f($this->resource, $ps, \count($points), $color);
        }
        else if ($n == 2) {
            \imageline($this->resource, $points[0]['x'], $points[0]['y'], $points[1]['x'], $points[1]['y'], $color);
        }
        else if ($n == 1) {
            \imagesetpixel($this->resource, $points[0]['x'], $points[0]['y'], $color);
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
        $color = $this->getColorId($color);
        $font  = $this->resolveFont($font, $fontSize);
        
        $this->applyOffsets($point);
        
        if (\is_int($font)) {
            \imagestring($this->resource, $font, $point['x'], $point['y'], $text, $color);
        }
        else {
            $point['y'] += $this->getTextHeight();
            \imagettftext($this->resource, $fontSize, 0, $point['x'], $point['y'], $color, $font, $text);
        }
    }
    
    /**
     * 
     * @return int
     */
    public function getTextHeight()
    {
        $font = $this->resolveFont($this->barcode->font, $this->barcode->fontSize);
        if (\is_int($font)) {
            return \imagefontheight($font);
        }
        else {
            $box = \imagettfbbox($this->barcode->fontSize, 0, $this->barcode->font, 'M');
            return \abs($box[7] - $box[1]);
        }
    }

    /**
     * 
     * @param string $text
     * @return int
     */
    public function getTextWidth($text = null)
    {
        $font = $this->resolveFont($this->barcode->font, $this->barcode->fontSize);
        $text = \coalesce($text, $this->barcode->getDataToDisplay());
        
        if (\is_int($font)) {
            return \imagefontwidth($font) * \strlen($text);
        }
        else {
            $box = \imagettfbbox($this->barcode->fontSize, 0, $this->barcode->font, $text);
            return \abs($box[2] - $box[0]);
        }
    }

    /**
     * Allows use a another image where barcode will be drawed.
     * 
     * $source can be a GD resource or a image file path.
     * 
     * @param mixed $source
     * @return ImageRenderer
     */
    public function setResource($source)
    {
        if (\is_resource($source) && \strpos('gd', \get_resource_type($source)) !== false) {
            $this->external = $source;
        }
        else if (\file_exists($source)) {
            $this->external = \imagecreatefromstring(\file_get_contents($source));
        }
        else {
            $this->external = null;
        }
        return $this;
    }

    /**
     * 
     */
    protected function initResource()
    {
        if (!$this->external) {
            $width          = $this->getTotalWidth();
            $height         = $this->getTotalHeight();
            $this->resource = \imagecreatetruecolor($width, $height);
            $this->colors   = [];
        }
        else {
            $this->resource =& $this->external;
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
}
