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
        
        $color = $this->registerColor($color);
        $this->applyOffsets($point);
        
        $point2 = [
            $point[0] + $width - 1,
            $point[1] + $height - 1
        ];
        
        if ($point2[0] > $point[0] && $point2[1] > $point[1]) {
            $f = $filled ? '\\imagefilledrectangle' : '\\imagerectangle';
            $f($this->resource, $point[0], $point[1], $point2[0], $point2[1], $color);
        }
        else if ($point2[0] == $point[0] || $point2[1] == $point[1]) {
            \imageline($this->resource, $point[0], $point[1], $point2[0], $point2[1], $color);
        }
        else if ($point === $point2) {
            \imagesetpixel($this->resource, $point[0], $point[1], $color);
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
        
        $color     = $this->registerColor($color);
        $font      = $this->resolveFont($font, $fontSize);
        if (\is_int($font)) {
            $font = $this->makeTextImage($font, $fontSize, $text);
        }
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
        else if (\is_resource($font)) {
            \imagecopymerge(
                    $this->resource,
                    $font,
                    $point[0], $point[1],
                    0, 0,
                    \imagesx($font), \imagesy($font),
                    100
                );
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
     * @return self
     */
    public function setResource($resource)
    {
        if (\is_resource($resource) && \strpos('gd', \get_resource_type($resource)) !== false) {
            $this->resource = $resource;
        }
        else if (\is_scalar($resource) && \file_exists((string)$resource)) {
            $this->resource = \imagecreatefromstring(\file_get_contents($resource));
        }
        else {
            throw new Exception('Invalid image resource!');
        }
        $this->setOption('merge', true);
        return $this;
    }

    /**
     * 
     */
    protected function initResource()
    {
        $width  = $this->barcode->getTotalWidth();
        $height = $this->barcode->getTotalHeight();
        
        if (!$this->resource || !$this->options['merge']) {
            $this->resource = $this->createResource($width, $height);
        }
        $this->resizeResource($width, $height);
        
        // Fill barcode's background
        $this->drawRect(
            [0, 0], $width, $height, $this->barcode->backColor, true
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
            $resource = $this->createResource($newwidth, $newheight);
            \imagecopymerge($resource, $this->resource, 0, 0, 0, 0, $oldwidth, $oldheight, 100);
            \imagedestroy($this->resource);
            $this->resource = $resource;
        }
    }
    
    /**
     * Create a new image resource.
     * 
     * @param int $width
     * @param int $height
     * @param bool $fill Fill image with render background color?
     * @return resource
     */
    protected function createResource($width, $height)
    {
        $resource = \imagecreatetruecolor($width, $height);
        $color = $this->registerColor($this->options['backcolor'], $resource);
        \imagefill($resource, 0, 0, $color);
        return $resource;
    }

    /**
     * Register a color on given resource and return its pallete index.
     * 
     * If a color is already registered, return only the index.
     * 
     * If $resource was null, default resource will be used.
     * 
     * @param int $color
     * @param resource $resource
     * @return int
     */
    protected function registerColor($color, $resource = null)
    {
        if (!$resource) {
            $resource =& $this->resource;
        }
        
        $alpha = null;
        if ($color < 0) {
            $color *= -1;
            $alpha  = 127;
            $exact  = '\\imagecolorexactalpha';
            $alloc  = '\\imagecolorallocatealpha';
            \imagealphablending($resource, true);
            \imagesavealpha($resource, true);
        }
        else {
            $exact  = '\\imagecolorexact';
            $alloc  = '\\imagecolorallocate';
        }
        
        $args = self::colorToRgb($color);
        if ($alpha != null) {
            $args[] = $alpha;
        }
        \array_unshift($args, $resource);
        
        $i    = \call_user_func_array($exact, $args);
        if ($i == -1) {
            $i = \call_user_func_array($alloc, $args);
        }
        
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
            return 4;
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
     * Create a resized text image when using internal GD fonts.
     * 
     * @param int $font
     * @param int $fontSize
     * @param string $text
     * @return resource
     */
    protected function makeTextImage($font, $fontSize, $text)
    {
        $height    = \imagefontheight($font);
        $width     = $this->getTextWidth($font, $fontSize, $text);
        
        if (!$width) {
            $width = 1;
        }
        
        $resource  = $this->createResource($width, $height);
        $foreColor = $this->registerColor($this->barcode->foreColor, $resource);
        $backColor = $this->registerColor($this->barcode->backColor, $resource);
        \imagefill($resource, 0, 0, $backColor);
        \imagestring($resource, $font, 0, 0, $text, $foreColor);
        
        $factor    = ($fontSize / .75) / $height;
        $newwidth  = \round($width * $factor);
        $newheight = \round($height * $factor);
        
        if ($newheight > 0 && $newwidth > 0) {
            $resized   = $this->createResource($newwidth, $newheight);
            \imagecopyresampled($resized, $resource, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            return $resized;
        }
        
        return $resource;
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
        else if (\is_resource($font)) {
            return \imagesx($font);
        }
        else {
            $box = \imagettfbbox($fontSize, 0, $font, $text);
            return \abs($box[2] - $box[0]);
        }
    }
}
