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
            $from  = self::getUnitValue($from);
            $to    = self::getUnitValue($to);
            $value = ($value / $to) * $from;
        }
        return $value;
    }
    
    /**
     * Returns the unit value.
     * 
     * If unit is unknown, return 0.
     * 
     * @param string $unit
     * @return number
     */
    public static function getUnitValue($unit)
    {
        return \array_get(self::$units, $unit, 0);
    }
    
    /**
     * Returns the unit string that represents the value given.
     * 
     * If value is not found, return false.
     * 
     * @param number $value
     * @return string
     */
    public static function getUnitByValue($value)
    {
        return \array_search($value, self::$units);
    }
}
