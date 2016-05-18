<?php

namespace Zeus\Barcode;

use Zeus\Barcode\AbstractChecksumBarcode;

/**
 * Implements a Code93 barcode standard.
 * 
 * Supports full ASCII mode.
 *
 * @author rafaelsalvioni
 * @see http://www.barcodeisland.com/code93.phtml
 */
class Code93 extends AbstractChecksumBarcode
{
    /**
     * Encoding table
     * 
     * ($) -> x80
     * (%) -> x81
     * (/) -> x82
     * (+) -> x83
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0'    => '100010100', '1'    => '101001000', '2'    => '101000100',
        '3'    => '101000010', '4'    => '100101000', '5'    => '100100100',
        '6'    => '100100010', '7'    => '101010000', '8'    => '100010010',
        '9'    => '100001010', 'A'    => '110101000', 'B'    => '110100100',
        'C'    => '110100010', 'D'    => '110010100', 'E'    => '110010010',
        'F'    => '110001010', 'G'    => '101101000', 'H'    => '101100100',
        'I'    => '101100010', 'J'    => '100110100', 'K'    => '100011010',
        'L'    => '101011000', 'M'    => '101001100', 'N'    => '101000110',
        'O'    => '100101100', 'P'    => '100010110', 'Q'    => '110110100',
        'R'    => '110110010', 'S'    => '110101100', 'T'    => '110100110',
        'U'    => '110010110', 'V'    => '110011010', 'W'    => '101101100',
        'X'    => '101100110', 'Y'    => '100110110', 'Z'    => '100111010',
        '-'    => '100101110', '.'    => '111010100', ' '    => '111010010',
        '$'    => '111001010', '/'    => '101101110', '+'    => '101110110',
        '%'    => '110101110', "\x80" => '100100110', "\x81" => '111011010',
        "\x82" => '111010110', "\x83" => '100110010', '*'    => '101011110'
    ];
    
    /**
     * Extended table
     * 
     * @var array
     */
    protected static $extendedTable = [
        "\x00" => "\x81U", '@'  => "\x81V", '`' => "\x81W",
        "\x01" => "\x80A", '!'  => "\x82A", 'a' => "\x83A",
        "\x02" => "\x80B", '"'  => "\x82B", 'b' => "\x83B",
        "\x03" => "\x80C", '#'  => "\x82C", 'c' => "\x83C",
        "\x04" => "\x80D", '&'  => "\x82F", 'd' => "\x83D",
        "\x05" => "\x80E", '\'' => "\x82G", 'e' => "\x83E",
        "\x06" => "\x80F", '('  => "\x82H", 'f' => "\x83F",
        "\x07" => "\x80G", ')'  => "\x82I", 'g' => "\x83G",
        "\x08" => "\x80H", '*'  => "\x82J",
        "\x09" => "\x80I", ','  => "\x82L", 'i' => "\x83I",
        "\x0A" => "\x80J",               'j' => "\x83J",
        "\x0B" => "\x80K",               'k' => "\x83K",
        "\x0C" => "\x80L",               'l' => "\x83L",
        "\x0D" => "\x80M", 'm'  => "\x83M",
        "\x0E" => "\x80N", 'n'  => "\x83N",
        "\x0F" => "\x80O",               'o' => "\x83O",
        "\x10" => "\x80P", 'p'  => "\x83P",
        "\x11" => "\x80Q", 'q'  => "\x83Q",
        "\x12" => "\x80R", 'r'  => "\x83R",
        "\x13" => "\x80S", 's'  => "\x83S",
        "\x14" => "\x80T", 't'  => "\x83T",
        "\x15" => "\x80U", 'u'  => "\x83U",
        "\x16" => "\x80V", 'v'  => "\x83V",
        "\x17" => "\x80W", 'w'  => "\x83W",
        "\x18" => "\x80X", 'x'  => "\x83X",
        "\x19" => "\x80Y", 'y'  => "\x83Y",
        "\x1A" => "\x80Z", ':'  => "\x82Z", 'z'  => "\x83Z",
        "\x1B" => "\x81A", ';'  => "\x81F", '['  => "\x81K", '{'    => "\x81P",
        "\x1C" => "\x81B", '<<' => "\x81G", '\\' => "\x81L", '|'    => "\x81Q",
        "\x1D" => "\x81C", '='  => "\x81H", ']'  => "\x81M", '}'    => "\x81R",
        "\x1E" => "\x81D", '>'  => "\x81I", '^'  => "\x81N", '~'    => "\x81S",
        "\x1F" => "\x81E", '?'  => "\x81J", '_'  => "\x81O", "\x7F" => "\x81Z",
    ];
    
    /**
     * Stores the data converted to extended table
     * 
     * @var array
     */
    protected $extData;
    
    /**
     * Converts a barcode data to full ascii.
     * 
     * Uses $extData property as cache.
     * 
     * @param string $data
     * @return string
     */
    protected function resolveExtended($data)
    {
        if (empty($this->extData) || !isset($this->extData[$data])) {
            $ext     =& self::$extendedTable;
            $extData = \preg_replace_callback('/[^A-Z0-9\-. \$\+\/%]/', function ($m) use ($ext)
            {
                return $ext[$m[0]];
            }, $data);
            $this->extData[$data] = $extData;
        }
        return $this->extData[$data];
    }
    
    /**
     * 
     * @param string $data
     * @return string
     */
    protected function calcChecksum($data)
    {
        $data  = $this->resolveExtended($data);
        $data  = \str_split($data);
        $enc   = \array_keys(self::$encodingTable);
        $flip  = \array_flip($enc);
                
        foreach ($data as &$char) {
            $char = $flip[$char];
        }
        
        $sum    = self::sumCrescentWeight($data, 1, 20);
        $mod    = $sum % 47;
        $check  = $enc[$mod];
        
        $data[] = $mod;
        $sum    = self::sumCrescentWeight($data, 1, 15);
        $check .= $enc[($sum % 47)];
        
        return $check;
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return \preg_match('/^[\x00-\x7f]+$/', $data);
    }
    
    /**
     * 
     * @param string $data
     * @param mixed $cleanData
     * @return string
     */
    protected function extractChecksum($data, &$cleanData)
    {
        $check     = \substr_remove($data, -2);
        $cleanData = $data;
        return $check;
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $data    = $this->resolveExtended($data);
        $data    = "*$data*";
        $encoded = '';

        while (!empty($data)) {
            $char     = \substr_remove($data, 0, 1);
            $encoded .= self::$encodingTable[$char];
        }
        
        $encoded .= '1';
        return $encoded;
    }
}
