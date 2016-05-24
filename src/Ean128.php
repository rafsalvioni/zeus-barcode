<?php

namespace Zeus\Barcode;

/**
 * Implementantion of UCC/EAN-128 barcode standard.
 *
 * @author Rafael M. Salvioni
 */
class Ean128 extends Code128
{
    /**
     * Insert FNC1 on second position of code's array.
     * 
     * @param string $data
     * @return int[]
     */
    protected function convertToCodes($data)
    {
        $codes = parent::convertToCodes($data);
        $start = \array_shift($codes);
        \array_unshift($codes, $start, 102);
        return $codes;
    }
}
