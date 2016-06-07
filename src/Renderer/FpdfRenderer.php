<?php

namespace Zeus\Barcode\Renderer;

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
        $this->applyOffsets($point);
        
        list($r, $g, $b) = self::colorToRgb($color);
        $this->resource->SetDrawColor($r, $g, $b);
        $this->resource->SetFillColor($r, $g, $b);
        
        $this->convertPointUnit($point);

        $this->resource->Rect(
            $point[0],
            $point[1],
            $this->convertUnit($width),
            $this->convertUnit($height),
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
        
        $point[1] += $this->convertUnit($fontSize, 'pt');
        $this->resource->Text($point[0], $point[1], $text);
        
        return $this;
    }
    
    /**
     * Allows use a another PDF where barcode will be drawed.
     * 
     * $resource should be a FPDF instance.
     * 
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
            $this->totalHeight   = 0;
            $resource =& $pdf;
        }
        if (!($resource instanceof \FPDF)) {
            throw new Exception('Invalid PDF resource. Should be a FPDF object!');
        }
        $this->resource = $resource;
        $this->unit     = self::getResourceUnit($resource);
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
     * Get the user unit used in FPDF resource.
     * 
     * @param \FPDF $resource
     * @return string
     */
    protected static function getResourceUnit(\FPDF $resource)
    {
        $arr  = (array)$resource;
        $key  = "\x0*\x0k";
        $val  = $arr[$key];
        $unit = \array_search($val, self::$units, true);
        return $unit;
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
        $this->addPage($height);
        
        // Fill barcode's background
        $this->drawRect(
            [0, 0], $width, $height, $this->barcode->backColor, true
        );
    }
    
    /**
     * Adds a page if necessary.
     * 
     * @param number $height Additional height, in pixels
     */
    protected function addPage($height)
    {
        $height = $this->convertUnit($height);
        $offset = $this->convertUnit($this->options['offsettop']);
        $total  = $height + $offset;
        
        while ($total > $this->totalHeight) {
            $this->totalHeight += $this->addOrImportPage();
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
     * @return number
     */
    protected function addOrImportPage()
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
            $this->resource->AddPage();
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
        $point[1] += self::convertToUnit($this->resource->GetY(), $this->unit, 'px');
    }
    
    /**
     * Convert a value to resource unit.
     * 
     * @param number $value
     * @param string $from Value unit
     * @return number
     */
    protected function convertUnit($value, $from = 'px')
    {
        return self::convertToUnit($value, $from, $this->unit);
    }

    /**
     * Convert coords to resource unit.
     * 
     * @param array $point
     * @param string $unit Coords. Unit
     */
    protected function convertPointUnit(array &$point, $unit = 'px')
    {
        $point[0] = $this->convertUnit($point[0], $unit);
        $point[1] = $this->convertUnit($point[1], $unit);
    }
}
