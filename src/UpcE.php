<?php

namespace Zeus\Barcode;

/**
 * Implements a UPC-E barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/upce.phtml
 */
class UpcE extends AbstractChecksumBarcode
{
    use EanHelperTrait;
    
    /**
     * Parity table
     * 
     * 1 => Even
     * 0 => Odd
     * 
     * @var array
     */
    protected static $parityTable = [
        '0' => [[1, 1, 0, 0, 0, 0], [0, 0, 0, 1, 1, 1]],
        '1' => [[1, 1, 0, 1, 0, 0], [0, 0, 1, 0, 1, 1]],
        '2' => [[1, 1, 0, 0, 1, 0], [0, 0, 1, 1, 0, 1]],
        '3' => [[1, 1, 0, 0, 0, 1], [0, 0, 1, 1, 1, 0]],
        '4' => [[1, 0, 1, 1, 0, 0], [0, 1, 0, 0, 1, 1]],
        '5' => [[1, 0, 0, 1, 1, 0], [0, 1, 1, 0, 0, 1]],
        '6' => [[1, 0, 0, 0, 1, 1], [0, 1, 1, 1, 0, 0]],
        '7' => [[1, 0, 1, 0, 1, 0], [0, 1, 0, 1, 0, 1]],
        '8' => [[1, 0, 1, 0, 0, 1], [0, 1, 0, 1, 1, 0]],
        '9' => [[1, 0, 0, 1, 0, 1], [0, 1, 1, 0, 1, 0]],
    ];
    
    /**
     * Padding zeros left on $data to complete the necessary length.
     * 
     * @param string $data
     * @param bool $hasChecksum
     */
    public function __construct($data, $hasChecksum = true)
    {
        $data = self::zeroLeftPadding($data, $hasChecksum ? 8 : 7);
        parent::__construct($data, $hasChecksum);
    }

    /**
     * Separates by a space the system number and checksum digit.
     * 
     * @return string
     */
    public function getPrintableData()
    {
        $data = parent::getPrintableData();
        return $data{0} . ' ' . \substr($data, 1, -1) . ' ' . \substr($data, -1);
    }

    /**
     * Converts this barcode to a UPC-A barcode.
     * 
     * @return UpcA
     */
    public function toUpcA()
    {
        $data = $this->toUpcaData($this->data, true);
        return new UpcA($data, true);
    }
    
    /**
     * Converts a UPC-E data to UPC-A.
     * 
     * @param string $data UPC-E data
     * @param bool $hasChecksum Has checksum?
     * @return string
     */
    protected function toUpcaData($data, $hasChecksum = false)
    {
        $check = $hasChecksum ? $this->extractChecksum($data, $data) : '';
        $last  = \substr($data, -1);
        $upce  = \substr($data, 1);
        $data  = $data{0};
        
        if ($last == '0' || $last == '1' || $last == '2') {
            $data .= \substr($upce, 0, 2) . $last . '0000' . \substr($upce, 2, 3);
        }
        else if ($last == '3') {
            $data .= \substr($upce, 0, 3) . '00000' . \substr($upce, 3, 2);
        }
        else if ($last == '4') {
            $data .= \substr($upce, 0, 4) . '00000' . $upce{4};
        }
        else {
            $data .= \substr($upce, 0, 5) . '0000' . $last;
        }
        
        $data .= $check;
        return $data;
    }

    /**
     * 
     * @param string $data
     */
    protected function calcChecksum($data)
    {
        $data = $this->toUpcaData($data, false);
        return self::checkSumMod10($data);
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return \preg_match("/^[01][0-9]{7}$/", $data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $encoded   = '';
        $check     = $this->extractChecksum($data, $data);
        $parityTab =& self::$parityTable[$check][$data{0}];
        
        for ($i = 1; $i < 7; $i++) {
            $parity   = $parityTab[$i - 1];
            $encoded .= self::$encodingTable[$data{$i}][$parity];
        }
        
        $encoded  = '101' . $encoded . '010101';
        
        return $encoded;
    }
}
