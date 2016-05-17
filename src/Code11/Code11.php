<?php

namespace Zeus\Barcode\Code11;

/**
 * Description of Code11
 *
 * @author rafaelsalvioni
 */
final class Code11
{
    /**
     * Try to return the best Code11 instance for given parameters.
     * 
     * @param string $data
     * @param bool $hasChecksum
     * @return AbstractCode11
     */
    public static function factory($data, $hasChecksum = true)
    {
        if ($hasChecksum) {
            try {
                return new Code11K($data, true);
            }
            catch (Exception $ex) {
                // noop
            }
            return new Code11C($data, true);
        }
        else if (\strlen($data) > 9) {
            return new Code11K($data, false);
        }
        else {
            return new Code11C($data, false);
        }
    }
}
