<?php

namespace Zeus\Barcode;

use Zeus\Barcode\AbstractBarcode;

/**
 * Implements a Code128 barcode standard.
 * 
 * Provides a automatic and optimized switching for A, B and C charsets
 * to use.
 * 
 * Support caracters for ASCII extended too.
 * 
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code128.phtml
 */
class Code128 extends AbstractBarcode
{
    /**
     * Encoding table
     * 
     * @var array
     */
    protected static $encodingTable = [
        '11011001100', '11001101100', '11001100110', '10010011000',
        '10010001100', '10001001100', '10011001000', '10011000100',
        '10001100100', '11001001000', '11001000100', '11000100100',
        '10110011100', '10011011100', '10011001110', '10111001100',
        '10011101100', '10011100110', '11001110010', '11001011100',
        '11001001110', '11011100100', '11001110100', '11101101110',
        '11101001100', '11100101100', '11100100110', '11101100100',
        '11100110100', '11100110010', '11011011000', '11011000110',
        '11000110110', '10100011000', '10001011000', '10001000110',
        '10110001000', '10001101000', '10001100010', '11010001000',
        '11000101000', '11000100010', '10110111000', '10110001110',
        '10001101110', '10111011000', '10111000110', '10001110110',
        '11101110110', '11010001110', '11000101110', '11011101000',
        '11011100010', '11011101110', '11101011000', '11101000110',
        '11100010110', '11101101000', '11101100010', '11100011010',
        '11101111010', '11001000010', '11110001010', '10100110000',
        '10100001100', '10010110000', '10010000110', '10000101100',
        '10000100110', '10110010000', '10110000100', '10011010000',
        '10011000010', '10000110100', '10000110010', '11000010010',
        '11001010000', '11110111010', '11000010100', '10001111010',
        '10100111100', '10010111100', '10010011110', '10111100100',
        '10011110100', '10011110010', '11110100100', '11110010100',
        '11110010010', '11011011110', '11011110110', '11110110110',
        '10101111000', '10100011110', '10001011110', '10111101000',
        '10111100010', '11110101000', '11110100010', '10111011110',
        '10111101110', '11101011110', '11110101110', '11010000100',
        '11010010000', '11010011100'
    ];

    /**
     * Charsets table
     * 
     * @var array
     */
    protected static $charSets = [
        'A' => [
            ' ', '!', '"', '#', '$', '%', '&', "'",
            '(', ')', '*', '+', ',', '-', '.', '/',
            '0', '1', '2', '3', '4', '5', '6', '7',
            '8', '9', ':', ';', '<', '=', '>', '?',
            '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
            'X', 'Y', 'Z', '[', '\\', ']', '^', '_',
            "\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07",
            "\x08", "\x09", "\x0A", "\x0B", "\x0C", "\x0D", "\x0E", "\x0F",
            "\x10", "\x11", "\x12", "\x13", "\x14", "\x15", "\x16", "\x17",
            "\x18", "\x19", "\x1A", "\x1B", "\x1C", "\x1D", "\x1E", "\x1F",
            'FNC3', 'FNC2', 'SHIFT', 'Code C', 'Code B', 'FNC4', 'FNC1',
            'START A', 'START B', 'START C', 'STOP'
        ],
        'B' => [
            ' ', '!', '"', '#', '$', '%', '&', "'",
            '(', ')', '*', '+', ',', '-', '.', '/',
            '0', '1', '2', '3', '4', '5', '6', '7',
            '8', '9', ':', ';', '<', '=', '>', '?',
            '@', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
            'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
            'X', 'Y', 'Z', '[', '\\', ']', '^', '_',
            '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g',
            'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
            'p', 'q', 'r', 's', 't', 'u', 'v', 'w',
            'x', 'y', 'z', '{', '|', '}', '~', "\x7F",
            'FNC3', 'FNC2', 'SHIFT', 'Code C', 'FNC4', 'Code A', 'FNC1',
            'START A', 'START B', 'START C', 'STOP',
        ],
        'C' => [
            '00', '01', '02', '03', '04', '05', '06', '07', '08', '09',
            '10', '11', '12', '13', '14', '15', '16', '17', '18', '19',
            '20', '21', '22', '23', '24', '25', '26', '27', '28', '29',
            '30', '31', '32', '33', '34', '35', '36', '37', '38', '39',
            '40', '41', '42', '43', '44', '45', '46', '47', '48', '49',
            '50', '51', '52', '53', '54', '55', '56', '57', '58', '59',
            '60', '61', '62', '63', '64', '65', '66', '67', '68', '69',
            '70', '71', '72', '73', '74', '75', '76', '77', '78', '79',
            '80', '81', '82', '83', '84', '85', '86', '87', '88', '89',
            '90', '91', '92', '93', '94', '95', '96', '97', '98', '99',
            'Code B', 'Code A', 'FNC1', 'START A', 'START B', 'START C', 'STOP'
        ]
    ];
    
    /**
     * Cache of converted data
     *
     * @var array
     */
    protected static $converted = [];

    /**
     * Choose the better charset for $data.
     * 
     * Return the charset letter (A, B or C). $part is the string
     * to be converted using returned charset and it is extracted
     * from $data.
     * 
     * If anyone charset was choosed, returned a empty string.
     * 
     * @param string $data
     * @param string $part
     * @param string $curCharset Current charset in use, if there was one
     * @return string
     */
    protected function chooseCharset(&$data, &$part, $curCharset = null)
    {
        $tests = [
            '/^\d{2,3}$/'                     => !$curCharset ? 'C' : $curCharset,
            '/^\d{4,}/'                       => 'C',
            '/^\d{0,3}[\x00-\x2f\x40-\x60]+/' => 'A',
            '/^\d{0,3}[\x20-\x2f\x40-\xff]+/' => 'B',
            '/^\d/'                           => $curCharset && $curCharset != 'C'
                                                ? $curCharset
                                                : 'B',
        ];
        
        $charset = $match = null;
        $length  = 0;
        
        foreach ($tests as $regex => $testCharset) {
            if (!\preg_match($regex, $data, $match)) {
                continue;
            }
            $n = \strlen($match[0]);
            if ($testCharset == 'C' && ($n % 2) > 0) {
                $n--;
                if ($curCharset && $curCharset != 'C') {
                    $charset = $curCharset;
                    $length  = 1;
                    break;
                }
            }
            if ($n > $length) {
                $charset = $testCharset;
                $length  = $n;
                if ($match[0] == $data) {
                    break;
                }
            }
        }
        
        if ($charset) {
            $part = \substr_remove($data, 0, $length);
        }
        return $charset;
    }

    /**
     * Converts a string to Code128 codes.
     * 
     * @param string $data
     * @return array
     */
    protected function convertToCodes($data)
    {
        $idx = \crc32($data);
        if (isset(self::$converted[$idx])) {
            return self::$converted[$idx];
        }
        
        $codes      = [];
        $curCharset = '';
        $part       = '';
        
        while (!empty($data) && ($charset = $this->chooseCharset($data, $part, $curCharset)) != '') {
            if ($curCharset != $charset) {
                if (empty($codes)) {
                    $codes[] = \array_search("START $charset", self::$charSets['C']);
                }
                else {
                    $codes[] = \array_search("Code $charset", self::$charSets[$curCharset]);
                }
                $curCharset = $charset;
            }
            
            $table =& self::$charSets[$curCharset];
            
            if ($curCharset == 'C'){
                $part  = \str_split($part, 2);
                $part  = \array_map('\\intval', $part);
                $codes = \array_merge($codes, $part);
            }
            else {
                $part  = \str_split($part, 1);
                foreach ($part as &$char) {
                    $ord = \ord($char);
                    if ($ord > 127) {
                        $codes[] = \array_search('FNC4', $table);
                        $char    = \chr($ord - 128);
                    }
                    $code = \array_search($char, $table);
                    $codes[] = $code;
                }
            }
        }
        
        self::$converted[$idx] = $codes;
        return $codes;
    }
    
    /**
     * Calcs the checksum code.
     * 
     * @param string $data
     * @return int
     */
    protected function calcChecksumCode($data)
    {
        $codes = $this->convertToCodes($data);
        $sum   = self::sumDecrescentWeight($codes, \count($codes) - 1, 0) + $codes[0];
        return ($sum % 103);
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return \preg_match('/^[\x00-\xff]+$/', $data);
    }

    /**
     * 
     * @param EncoderInterface $encoder
     * @param string $data
     */
    protected function encodeData(Encoder\EncoderInterface &$encoder, $data)
    {
        $codes   = $this->convertToCodes($data);
        $codes[] = $this->calcChecksumCode($data);
        
        foreach ($codes as &$code) {
            $encoded = self::$encodingTable[$code];
            $encoder->addBinary($encoded);
        }
        
        $encoder->addBinary('1100011101011');
    }
}
