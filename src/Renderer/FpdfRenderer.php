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
     * PDF element
     * 
     * @var \FPDF
     */
    protected $resource;
    /**
     * Unit conversor for FPDP units
     * 
     * @var number
     */
    protected $conversor;
    /**
     * Total height of current resource
     * 
     * @var number
     */
    protected $totalHeight;
    /**
     * Pages to be imported from external PDF
     * 
     * @var int
     */
    protected $pagesToImport = 0;

    /**
     * 
     */
    public function render()
    {
        echo $this->getResource()->Output('', \uniqid() . '.pdf');
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
        
        $this->resource->Rect(
            $point[0] * $this->conversor,
            $point[1] * $this->conversor,
            $width    * $this->conversor,
            $height   * $this->conversor,
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
        
        list($r, $g, $b) = self::colorToRgb($color);
        $this->resource->SetDrawColor($r, $g, $b);
        $this->resource->SetFont($font, '', $fontSize);
        
        $textWidth = $this->resource->GetStringWidth($text) / $this->conversor;
        switch ($align) {
            case 'center':
                $point[0] -= ($textWidth / 2);
                break;
            case 'right':
                $point[0] -= $textWidth;
                break;
        }
        
        $point[1] += ($fontSize + 1);
        $this->resource->Text(
            $point[0] * $this->conversor,
            $point[1] * $this->conversor,
            $text
        );
        return $this;
    }
    
    /**
     * Allows define a external PDF resource to be used.
     * 
     * $resource can be a \FPDF object or a PDF file path.
     * 
     * @param mixed $resource
     * @return self
     * @throws Exception
     */
    public function setResource($resource)
    {
        if (\is_scalar($resource)) {
            $resource = (string)$resource;
            if (!\file_exists($resource)) {
                throw new Exception("File \"$resource\" not found!");
            }
            $pdf                 = new \FPDI();
            $this->pagesToImport = $pdf->setSourceFile($resource);
            $resource =& $pdf;
        }
        if (!($resource instanceof \FPDF)) {
            throw new Exception('Resource isn\'t a valid PDF resource');
        }
        $this->loadResource($resource);
        $this->setOption('merge', true);
        return $this;
    }
    
    /**
     * 
     * @return \FPDF
     */
    public function getResource()
    {
        $this->checkStarted();
        while ($this->pagesToImport > 0) {
            $this->addOrImportPage();
        }
        return $this->resource;
    }

    /**
     * Returns the FPDF conversor factor.
     * 
     * @param \FPDF $pdf
     * @return number
     */
    protected static function getConversor(\FPDF $pdf)
    {
        $arr = (array)$pdf;
        $key = "\x0*\x0k";
        $val = \array_get($arr, $key, 1);
        return (1 / $val) * .75;
    }

    /**
     * 
     */
    protected function initResource()
    {
        $width  = $this->barcode->getTotalWidth();
        $height = $this->barcode->getTotalHeight();

        if (!$this->resource || !$this->options['merge']) {
            $this->loadResource(new \FPDF());
        }
        $this->addNeededPages($height);
        
        $this->drawRect(
            [0, 0], $width, $height, $this->barcode->backColor, true
        );
    }
    
    /**
     * Add the needed pages to supports the current offsetTop + barcode height.
     * 
     * @param number $height
     */
    protected function addNeededPages($height)
    {
        $height *= $this->conversor;
        $offset  = $this->options['offsettop'] * $this->conversor;
        $total   = $height + $offset;
        $diff    = $total - $this->totalHeight;
        
        while ($total > $this->totalHeight) {
            $this->addOrImportPage();
        }
        
        if ($diff > 0 && $diff < $height) {
            $this->options['offsettop'] += (int)\ceil(
                ($diff + $height) / $this->conversor
            );
            $offset = 0;
        }
        else {
            $pageHeight = $this->resource->GetPageHeight();
            $offsetY    = $this->totalHeight - $pageHeight;
            if ($offset >= $offsetY) {
                $offset -= $offsetY;
            }
        }
        $this->resource->SetY($offset);
    }
    
    /**
     * Import a external page, if has one, or add a blank page.
     * 
     * Increments $totalHeight property and decrements $pagesToImport property.
     * 
     * @return self
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
        $this->totalHeight += $this->resource->GetPageHeight();
        return $this;
    }

    /**
     * Load object using info of a FDPF object.
     * 
     * @param \FPDF $pdf
     */
    protected function loadResource(\FPDF $pdf)
    {
        $this->resource    = $pdf;
        $this->conversor   = self::getConversor($pdf);
        $this->totalHeight = $pdf->PageNo() * $pdf->GetPageHeight();
    }

    /**
     * 
     * @param array $point
     */
    protected function applyOffsets(array &$point)
    {
        $point[0] += $this->options['offsetleft'];
        $point[1] += $this->resource->GetY() / $this->conversor;
    }
}
