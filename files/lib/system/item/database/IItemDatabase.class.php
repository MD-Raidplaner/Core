<?php

namespace rp\system\item\database;

use wcf\data\language\Language;

/**
 * Default interface for item database.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
interface IItemDatabase
{
    /**
     * Return item array, with all information about an item.
     * 
     * The returned array must at least contain the following keys:
     * - 'icon' (string)
     * - 'iconExtension' (string)
     * - 'iconURL' (string)
     * - 'name' (string)
     * - 'tooltip' (string)
     * 
     * Example structure:
     * $itemData = [
     *     'icon' => '',
     *     'iconExtension' => '',
     *     'iconURL' => '',
     *     'name' => '',
     *     'tooltip' => '',
     * ];
     * 
     * Additional fields may be included in the array, but the above keys are required.
     */
    public function getItemData(string|int $itemID, ?Language $language = null, ?string $additionalData = null): ?array;

    /**
     * Searches an item id for an item name.
     */
    public function searchItemID(string $itemName, ?Language $language = null): int|string;
}
