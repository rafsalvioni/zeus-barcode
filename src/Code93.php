<?php

namespace Zeus\Barcode;

use Zeus\Barcode\Encoder\EncoderInterface;

/**
 * Implements a Code93 barcode standard.
 * 
 * Supports full ASCII mode.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code93.phtml
 */
class Code93 extends AbstractBarcode
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
        "\x00" => "\x81U", "\x01" => "\x80A", "\x02" => "\x80B", "\x03" => "\x80C",
        "\x04" => "\x80D", "\x05" => "\x80E", "\x06" => "\x80F", "\x07" => "\x80G",
        "\x08" => "\x80H", "\x09" => "\x80I", "\x0a" => "\x80J", "\x0b" => "\x80K",
        "\x0c" => "\x80L", "\x0d" => "\x80M", "\x0e" => "\x80N", "\x0f" => "\x80O",
        "\x10" => "\x80P", "\x11" => "\x80Q", "\x12" => "\x80R", "\x13" => "\x80S",
        "\x14" => "\x80T", "\x15" => "\x80U", "\x16" => "\x80V", "\x17" => "\x80W",
        "\x18" => "\x80X", "\x19" => "\x80Y", "\x1a" => "\x80Z", "\x1b" => "\x81A",
        "\x1c" => "\x81B", "\x1d" => "\x81C", "\x1e" => "\x81D", "\x1f" => "\x81E",
        '!'    => "\x82A", '"'    => "\x82B", '#'    => "\x82C", '&'    => "\x82F",
        '\''   => "\x82G", '('    => "\x82H", ')'    => "\x82I", '*'    => "\x82J",
        ','    => "\x82L", ':'    => "\x82Z", ';'    => "\x81F", '<'    => "\x81G",
        '='    => "\x81H", '>'    => "\x81I", '?'    => "\x81J", '@'    => "\x81V",
        '['    => "\x81K", '\\'   => "\x81L", ']'    => "\x81M", '^'    => "\x81N",
        '_'    => "\x81O", '`'    => "\x81W", 'a'    => "\x83A", 'b'    => "\x83B",
        'c'    => "\x83C", 'd'    => "\x83D", 'e'    => "\x83E", 'f'    => "\x83F",
        'g'    => "\x83G", 'h'    => "\x83H", 'i'    => "\x83I", 'j'    => "\x83J",
        'k'    => "\x83K", 'l'    => "\x83L", 'm'    => "\x83M", 'n'    => "\x83N",
        'o'    => "\x83O", 'p'    => "\x83P", 'q'    => "\x83Q", 'r'    => "\x83R",
        's'    => "\x83S", 't'    => "\x83T", 'u'    => "\x83U", 'v'    => "\x83V",
        'w'    => "\x83W", 'x'    => "\x83X", 'y'    => "\x83Y", 'z'    => "\x83Z",
        '{'    => "\x81P", '|'    => "\x81Q", '}'    => "\x81R", '~'    => "\x81S",
        "\x7f" => "\x81Z",
    ];
    
    /**
     * Stores the data converted to extended table
     * 
     * @var array
     */
    protected static $extData = [];
    
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
        $idx = \crc32($data);
        if (!isset(self::$extData[$idx])) {
            $ext     =& self::$extendedTable;
            $extData = \preg_replace_callback('/[^A-Z0-9\-. \$\+\/%\x80-\x83]/', function ($m) use ($ext)
            {
                return $ext[$m[0]];
            }, $data);
            self::$extData[$idx] = $extData;
        }
        return self::$extData[$idx];
    }
    
    /**
     * 
     * @param string $data
     * @return string
     */
    protected function makeChecksum($data)
    {
        $data  = \str_split($data);
        $enc   = \array_keys(self::$encodingTable);
        $flip  = \array_flip($enc);
                
        foreach ($data as &$char) {
            $char = $flip[$char];
        }
        
        $sum    = self::sumCrescentWeight($data, 1, 20);
        $check  = (string)$enc[($sum % 47)];
        
        $data[] = $flip[$check[0]];
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
     * @param EncoderInterface $encoder
     * @param string $data
     */
    protected function encodeData(EncoderInterface &$encoder, $data)
    {
        $data  = $this->resolveExtended($data);
        $data .= $this->makeChecksum($data);
        $data  = "*$data*";

        while (!empty($data)) {
            $char    = \substr_remove($data, 0, 1);
            $encoded = self::$encodingTable[$char];
            $encoder->addBinary($encoded);
        }
       
        $encoder->addBinary('1');
    }
}
