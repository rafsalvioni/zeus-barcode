<?php

namespace Zeus\Barcode;

/**
 * Helper class to convert units used by barcodes and renderers.
 *
 * @author Rafael M. Salvioni
 */
class Measure
{
    /**
     * Units conversors
     * 
     * @var array
     */
    protected static $units = [
        'pt' => 1,
        'px' => 0.75,
        'mm' => 72/25.4,
        'cm' => 72/2.54,
        'in' => 72,
    ];
    
    /**
     * Convert values between units.
     * 
     * @param number $value
     * @param string $from From unit
     * @param string $to To unit
     * @return number
     */
    public static function convert($value, $from, $to)
    {
        if ($from != $to) {
            $from  = \array_get(self::$units, $from, 0);
            $to    = \array_get(self::$units, $to, 0);
            $value = ($value / $to) * $from;
        }
        return $value;
    }
}
