<?php

namespace Zeus\Barcode\Renderer;

use Zeus\Barcode\Measure;

/**
 * Renderer to draw barcodes as PDF using FPDF class.
 *
 * @author Rafael M. Salvioni
 */
class FpdfRenderer extends AbstractRenderer
{
    /**
     * Default typography unit used in FPDF
     * 
     */
    const DEFAULT_UNIT       = 'mm';
    
    /**
     * Unit used in current FPDF resource
     * 
     * @var string
     */
    protected $unit          = self::DEFAULT_UNIT;
    /**
     * Total resource height, expressed in current unit
     * 
     * @var number
     */
    protected $totalHeight   = 0;
    /**
     * External pages to import
     * 
     * @var int
     */
    protected $pagesToImport = 0;

    /**
     * Render
     * 
     */
    public function render()
    {
        $this->checkStarted();
        \header('Pragma: no-cache');
        $this->resource->Output('', uniqid() . '.pdf');
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
        
        if ($color < 0) {
            return $this;
        }

        $this->applyOffsets($point);
        
        list($r, $g, $b) = self::colorToRgb($color);
        $this->resource->SetDrawColor($r, $g, $b);
        $this->resource->SetFillColor($r, $g, $b);
        
        $this->convertPointUnit($point);

        $this->resource->Rect(
            $point[0],
            $point[1],
            $this->convertToResource($width),
            $this->convertToResource($height),
            $filled ? 'F' : 'D'
        );
        
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
        
        if (!$font) {
            $font = 'Arial';
        }
        else {
            $font = \preg_replace('/\.[a-z\d]{1,4}$/i', '', \basename($font));
        }

        $this->applyOffsets($point);
        $this->convertPointUnit($point);
                        
        list($r, $g, $b) = self::colorToRgb($color);
        $this->resource->SetTextColor($r, $g, $b);
        $this->resource->SetFont($font, '', $fontSize);
        $textWidth = $this->resource->GetStringWidth($text);
        
        switch ($align) {
            case 'center':
                $point[0] -= ($textWidth / 2);
                break;
            case 'right':
                $point[0] -= $textWidth;
                break;
        }
        
        $point[1] += $this->convertToResource($fontSize - 1, 'pt');
        $this->resource->Text($point[0], $point[1], $text);
        
        return $this;
    }
    
    /**
     * Allows use a another PDF where barcode will be drawed.
     * 
     * $resource should be a FPDF instance or a PDF file path.
     * If is a file, its pages will be imported on demand. To import all
     * remainder pages, use flush() method.
     * 
     * @see self::flush()
     * @param mixed $resource
     * @return self
     */
    public function setResource($resource)
    {
        if (\is_scalar($resource)) {
            $resource = (string)$resource;
            if (!\file_exists($resource)) {
                throw new Exception("File \"$resource\" not found!");
            }
            $pdf                 = new \FPDI('P', self::DEFAULT_UNIT);
            $this->pagesToImport = $pdf->setSourceFile($resource);
            $resource =& $pdf;
        }
        if (!($resource instanceof \FPDF)) {
            throw new Exception('Invalid PDF resource. Should be a FPDF object!');
        }
        $this->resource    = $resource;
        $this->unit        = self::extractResourceUnit($resource);
        $this->totalHeight = $resource->GetPageHeight() * $resource->PageNo();
        $this->setOption('merge', true);
        return $this;
    }
    
    /**
     * Flush all pages still not imported.
     * 
     * @return self
     */
    public function flush()
    {
        while ($this->pagesToImport > 0) {
            $this->totalHeight += $this->addOrImportPage();
        }
        return $this;
    }
    
    /**
     * Returns the string of unit used by current PDF resource.
     * 
     * @return string
     */
    public function getResourceUnit()
    {
        return $this->unit;
    }
    
    /**
     * Convert a value to unit used by current PDF resource.
     * 
     * @param number $value
     * @param string $from Source unit
     * @return number
     */
    public function convertToResource($value, $from = 'px')
    {
        return Measure::convert($value, $from, $this->unit);
    }
    
    /**
     * Convert a value in PDF resource's unit to another unit.
     * 
     * @param number $value
     * @param string $to To unit
     * @return number
     */
    public function convertFromResource($value, $to = 'px')
    {
        return Measure::convert($value, $this->unit, $to);
    }

    /**
     * Get the user unit used in FPDF resource.
     * 
     * @param \FPDF $resource
     * @return string
     */
    protected static function extractResourceUnit(\FPDF $resource)
    {
        $arr  = (array)$resource;
        $key  = "\x0*\x0k";
        $val  = $arr[$key];
        $unit = Measure::getUnitByValue($val);
        return $unit !== false ? $unit : self::DEFAULT_UNIT;
    }
    
    /**
     * 
     */
    protected function initResource()
    {
        $width  = $this->barcode->getTotalWidth();
        $height = $this->barcode->getTotalHeight();
        
        if (!$this->resource || !$this->options['merge']) {
            $this->resource      = new \FPDF('P', self::DEFAULT_UNIT);
            $this->unit          = self::DEFAULT_UNIT;
            $this->totalHeight   = 0;
            $this->pagesToImport = 0;
        }
        $this->addPage($width, $height);
        
        // Fill barcode's background
        $this->drawRect(
            [0, 0], $width, $height, $this->barcode->backColor, true
        );
    }
    
    /**
     * Adds a page if necessary.
     * 
     * @param number $width Additional width, in pixels
     * @param number $height Additional height, in pixels
     */
    protected function addPage($width, $height)
    {
        $height = $this->convertToResource($height);
        $width  = $this->convertToResource($width);
        $offset = $this->convertToResource($this->options['offsettop']);
        $total  = $height + $offset;
        
        while ($total > $this->totalHeight) {
            $this->totalHeight += $this->addOrImportPage($width, $height);
        }
        
        $pageHeight = $this->resource->GetPageHeight();
        $offsetY    = $this->totalHeight - $pageHeight;
        if ($offset >= $offsetY) {
            $offset -= $offsetY;
        }
        $this->resource->SetY($offset);
    }
    
    /**
     * Add a blank page or import a parse page, if has one.
     * 
     * Returns the height of new page.
     * 
     * @param number $width Needed width, in FPDF unit
     * @param number $height Needed height, in FPDF unit
     * @return number
     */
    protected function addOrImportPage($width, $height)
    {
        if ($this->pagesToImport > 0) {
            $tpl    = $this->resource->importPage($this->resource->PageNo() + 1);
            $size   = $this->resource->getTemplateSize($tpl);
            $orient = $size['w'] > $size['h'] ? 'L' : 'P';
            $this->resource->AddPage($orient, \array_values($size));
            $this->resource->useTemplate($tpl);
            $this->pagesToImport--;
        }
        else {
            $orient = $width > $height ? 'L' : 'P';
            $this->resource->AddPage($orient);
        }
        return $this->resource->GetPageHeight();
    }

    /**
     * Using FPDF SetY in $point[1]
     * 
     * @param array $point
     */
    protected function applyOffsets(array &$point)
    {
        $point[0] += $this->options['offsetleft'];
        $point[1] += Measure::convert($this->resource->GetY(), $this->unit, 'px');
    }
    
    /**
     * Convert coords to resource unit.
     * 
     * @param array $point
     * @param string $unit Coords. Unit
     */
    protected function convertPointUnit(array &$point, $unit = 'px')
    {
        $point[0] = $this->convertToResource($point[0], $unit);
        $point[1] = $this->convertToResource($point[1], $unit);
    }
}
