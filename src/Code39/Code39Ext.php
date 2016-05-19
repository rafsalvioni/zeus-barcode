<?php

namespace Zeus\Barcode\Code39;

/**
 * Implementation of Code39 using full ascii mode.
 *
 * @author Rafael M. Salvioni
 * @see http://www.barcodeisland.com/code39.phtml
 */
class Code39Ext extends Code39
{
    /**
     * Extended table
     * 
     * @var array
     */
    protected static $extendedTable = [
        "\x00" => '%U', "\x01" => '$A', "\x02" => '$B', "\x03" => '$C',
        "\x04" => '$D', "\x05" => '$E', "\x06" => '$F', "\x07" => '$G',
        "\x08" => '$H', "\x09" => '$I', "\x0a" => '$J', "\x0b" => '$K',
        "\x0c" => '$L', "\x0d" => '$M', "\x0e" => '$N', "\x0f" => '$O',
        "\x10" => '$P', "\x11" => '$Q', "\x12" => '$R', "\x13" => '$S',
        "\x14" => '$T', "\x15" => '$U', "\x16" => '$V', "\x17" => '$W',
        "\x18" => '$X', "\x19" => '$Y', "\x1a" => '$Z', "\x1b" => '%A',
        "\x1c" => '%B', "\x1d" => '%C', "\x1e" => '%D', "\x1f" => '%E',
        '!'    => '/A', '"'    => '/B', '#'    => '/C', '$'    => '/D',
        '%'    => '/E', '&'    => '/F', '\''   => '/G', '('    => '/H',
        ')'    => '/I', '*'    => '/J', '+'    => '/K', ','    => '/L',
        '/'    => '/O', ':'    => '/Z', ';'    => '%F', '<'    => '%G',
        '='    => '%H', '>'    => '%I', '?'    => '%J', '@'    => '%V',
        '['    => '%K', '\\'   => '%L', ']'    => '%M', '^'    => '%N',
        '_'    => '%O', '`'    => '%W', 'a'    => '+A', 'b'    => '+B',
        'c'    => '+C', 'd'    => '+D', 'e'    => '+E', 'f'    => '+F',
        'g'    => '+G', 'h'    => '+H', 'i'    => '+I', 'j'    => '+J',
        'k'    => '+K', 'l'    => '+L', 'm'    => '+M', 'n'    => '+N',
        'o'    => '+O', 'p'    => '+P', 'q'    => '+Q', 'r'    => '+R',
        's'    => '+S', 't'    => '+T', 'u'    => '+U', 'v'    => '+V',
        'w'    => '+W', 'x'    => '+X', 'y'    => '+Y', 'z'    => '+Z',
        '{'    => '%P', '|'    => '%Q', '}'    => '%R', '~'    => '%S',
        "\x7f" => '%Z',
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
            $extData = \preg_replace_callback('/[^A-Z0-9\\-\\. ]/', function ($m) use ($ext)
            {
                return $ext[$m[0]];
            }, $data);
            $this->extData[$data] = $extData;
        }
        return $this->extData[$data];
    }
    
    /**
     * Accepts all ascii (0-127) chars.
     * 
     * @param string $data
     * @return string
     */
    protected function checkData($data)
    {
        return \preg_match('/^[\x00-\x7f]+$/', $data);
    }

    /**
     * Uses converted data.
     * 
     * @param string $data
     * @return string
     */
    protected function encodeData($data)
    {
        $data = $this->resolveExtended($data);
        return parent::encodeData($data);
    }
}
