<?php

namespace rp\util;

use wcf\system\WCF;

/**
 * ontains raidplaner-related functions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPUtil
{
    /**
     * Formats the points
     */
    public static function formatPoints(float|int $points = 0): string
    {
        $precision = RP_ROUND_POINTS ? RP_ROUND_POINTS_PRECISION : 2;
        $locale = WCF::getLanguage()->getLocale();
        $formatter = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $precision);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $precision);

        return $formatter->format($points);
    }

    /**
     * Generates a unique key based on the item name and optional additional data.
     *
     * This method combines the provided item name and additional data (if any) into a single string,
     * then hashes the combined string using the SHA-256 algorithm to produce a unique key.
     * The key will be consistent for the same combination of item name and additional data.
     */
    public static function generateItemUniqueKey(string $itemName, ?string $additionalData = null): string
    {
        $combinedString = \sprintf(
            '%s:%s',
            $itemName,
            $additionalData ?? ''
        );

        return \hash('sha256', $combinedString);
    }
}
