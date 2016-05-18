<?php

namespace Zeus\Barcode\Code39;

use Zeus\Barcode\AbstractBarcode;
use Zeus\Barcode\TwoWidthInterface;
use Zeus\Barcode\TwoWidthTrait;

/**
 * Implementation of Code39 barcode standard, without checksum.
 * 
 * Supports full ASCII mode.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code39.phtml
 */
class Code39 extends AbstractBarcode implements TwoWidthInterface
{
    use TwoWidthTrait;

    /**
     * Encoding table
     * 
     * N -> narrow
     * W -> wide
     * 
     * @var array
     */
    protected static $encodingTable = [
        '0' => 'NNNWWNWNN', '1' => 'WNNWNNNNW',	'2' => 'NNWWNNNNW',
        '3' => 'WNWWNNNNN', '4' => 'NNNWWNNNW',	'5' => 'WNNWWNNNN',
        '6' => 'NNWWWNNNN', '7' => 'NNNWNNWNW',	'8' => 'WNNWNNWNN',
        '9' => 'NNWWNNWNN', 'A' => 'WNNNNWNNW',	'B' => 'NNWNNWNNW',
        'C' => 'WNWNNWNNN', 'D' => 'NNNNWWNNW', 'E' => 'WNNNWWNNN',
        'F' => 'NNWNWWNNN', 'G' => 'NNNNNWWNW', 'H' => 'WNNNNWWNN',
        'I' => 'NNWNNWWNN', 'J' => 'NNNNWWWNN', 'K' => 'WNNNNNNWW',
        'L' => 'NNWNNNNWW', 'M' => 'WNWNNNNWN', 'N' => 'NNNNWNNWW',
        'O' => 'WNNNWNNWN', 'P' => 'NNWNWNNWN', 'Q' => 'NNNNNNWWW',
        'R' => 'WNNNNNWWN', 'S' => 'NNWNNNWWN', 'T' => 'NNNNWNWWN',
        'U' => 'WWNNNNNNW', 'V' => 'NWWNNNNNW', 'W' => 'WWWNNNNNN',
        'X' => 'NWNNWNNNW', 'Y' => 'WWNNWNNNN', 'Z' => 'NWWNWNNNN',
        '-' => 'NWNNNNWNW', '.' => 'WWNNNNWNN', ' ' => 'NWWNNNWNN',
        '$' => 'NWNWNWNNN', '/' => 'NWNWNNNWN', '+' => 'NWNNNWNWN',
        '%' => 'NNNWNWNWN', '*' => 'NWNNWNWNN',
    ];
    
    /**
     * Extended table
     * 
     * @var array
     */
    protected static $extendedTable = [
        "\x00" => '%U', '@'  => '%V', '`' => '%W',
        "\x01" => '$A', '!'  => '/A', 'a' => '+A',
        "\x02" => '$B', '"'  => '/B', 'b' => '+B',
        "\x03" => '$C', '#'  => '/C', 'c' => '+C',
        "\x04" => '$D', '$'  => '/D', 'd' => '+D',
        "\x05" => '$E', '%'  => '/E', 'e' => '+E',
        "\x06" => '$F', '&'  => '/F', 'f' => '+F',
        "\x07" => '$G', '\'' => '/G', 'g' => '+G',
        "\x08" => '$H', '('  => '/H',
        "\x09" => '$I', ')'  => '/I', 'i' => '+I',
        "\x0A" => '$J', '*'  => '/J', 'j' => '+J',
        "\x0B" => '$K', '+'  => '/K', 'k' => '+K',
        "\x0C" => '$L', ','  => '/L', 'l' => '+L',
        "\x0D" => '$M', 'm'  => '+M',
        "\x0E" => '$N', 'n'  => '+N',
        "\x0F" => '$O', '/'  => '/O', 'o' => '+O',
        "\x10" => '$P', 'p'  => '+P',
        "\x11" => '$Q', 'q'  => '+Q',
        "\x12" => '$R', 'r'  => '+R',
        "\x13" => '$S', 's'  => '+S',
        "\x14" => '$T', 't'  => '+T',
        "\x15" => '$U', 'u'  => '+U',
        "\x16" => '$V', 'v'  => '+V',
        "\x17" => '$W', 'w'  => '+W',
        "\x18" => '$X', 'x'  => '+X',
        "\x19" => '$Y', 'y'  => '+Y',
        "\x1A" => '$Z', ':'  => '/Z', 'z'  => '+Z',
        "\x1B" => '%A', ';'  => '%F', '['  => '%K', '{'    => '%P',
        "\x1C" => '%B', '<<' => '%G', '\\' => '%L', '|'    => '%Q',
        "\x1D" => '%C', '='  => '%H', ']'  => '%M', '}'    => '%R',
        "\x1E" => '%D', '>'  => '%I', '^'  => '%N', '~'    => '%S',
        "\x1F" => '%E', '?'  => '%J', '_'  => '%O', "\x7F" => '%Z',
    ];
    
    /**
     * 
     * @param string $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setWideWidth(2);
    }
    
    /**
     * Returns a Code39 instance with addtional checksum mod 43 basead
     * on current barcode.
     * 
     * @return Code39Mod43
     */
    public function withChecksum()
    {
        return new Code39Mod43($this->data, false);
    }

    /**
     * 
     * @param string $data
     * @return bool
     */
    protected function checkData($data)
    {
        return preg_match('/^[\x00-\x7F]+$/', $data);
    }

    /**
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $ext  =& self::$extendedTable;
        $data = \preg_replace_callback('/[^A-Z0-9\\-\\. ]/', function ($m) use ($ext)
        {
            return $ext[$m[0]];
        }, $data);
        
        $data    = "*$data*";
        $data    = \str_split($data);
        $encoded = '';
        
        foreach ($data as &$char) {
            $encoded .= $this->encodeChar($char);
            $encoded .= $this->encodeWithWidth(true, false);
        }
        
        $encoded = \substr($encoded, 0, $this->narrowWidth * -1);
        
        return $encoded;
    }
    
    /**
     * Encodes a single char.
     * 
     * @param string $char
     * @return string
     */
    protected function encodeChar($char)
    {
        $encChar =& self::$encodingTable[$char];
        $bar     = true;
        $return  = '';
        
        for ($i = 0; $i < 9; $i++) {
            $return .= $this->encodeWithWidth($encChar{$i} == 'N', $bar);
            $bar     = !$bar;
        }
        
        return $return;
    }
}
