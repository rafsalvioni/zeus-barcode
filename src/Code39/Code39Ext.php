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
