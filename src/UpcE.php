<?php

namespace Zeus\Barcode;

/**
 * Implements a UPC-E barcode standard.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/upce.phtml
 */
class UpcE extends AbstractBarcode
{
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
     * Encoding table, by parity
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => ['0001101', '0100111'],
        '1' => ['0011001', '0110011'],
        '2' => ['0010011', '0011011'],
        '3' => ['0111101', '0100001'],
        '4' => ['0100011', '0011101'],
        '5' => ['0110001', '0111001'],
        '6' => ['0101111', '0000101'],
        '7' => ['0111011', '0010001'],
        '8' => ['0110111', '0001001'],
        '9' => ['0001011', '0010111'],
    ];
    
    /**
     * UPC-E barcodes are generated using a UPC-A barcode. So, we need one...
     * 
     * However, not all UPC-A barcodes can generate a UPC-E. In this case,
     * constructor will throw a exception.
     * 
     * @param UpcA $upca
     * @throws UpcEException
     */
    public function __construct(UpcA $upca)
    {
        $data    = $upca->getData(false);
        $system  = $data{0};
        $mfct    = \substr($data, 1, 5);
        $product = \substr($data, 6, 5);
        
        if (\preg_match('/[0-2]00$/', $mfct) && \preg_match('/00[0-9]{3}$/', $product)) {
            $data = \substr($mfct, 0, 2) . \substr($product, -3) . $mfct{2};
        }
        else if (\preg_match('/00$/', $mfct) && \preg_match('/000[0-9]{2}$/', $product)) {
            $data = \substr($mfct, 0, 3) . \substr($product, -2) . '3';
        }
        else if (\preg_match('/0$/', $mfct) && \preg_match('/0000[0-9]$/', $product)) {
            $data = \substr($mfct, 0, 4) . \substr($product, -1) . '4';
        }
        else if (\preg_match('/0000[5-9]$/', $product)) {
            $data = $mfct . \substr($product, -1);
        }
        else {
            throw new UpcEException('Unconvertable UPC-A barcode!');
        }
        
        $this->data     = $system . $data;
        $this->checksum = $upca->getChecksum();
    }

    /**
     * 
     * @param string $data
     */
    protected function calcChecksum($data)
    {
        //noop
    }

    /**
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return bool
     */
    protected function checkData($data, $hasChecksum = true)
    {
        $len = 7;
        if (!$hasChecksum) {
            $len--;
        }
        return \preg_match("/^[01][0-9]{{$len}}$/", $data);
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
        $last = \substr($this->data, -1);
        $upce = \substr($this->data, 1);
        $data = $this->data{0};
        
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
        
        $data .= $this->checksum;
        
        return new UpcA($data, true);
    }
}

/**
 * UpcE class exception
 * 
 */
class UpcEException extends Exception {}
