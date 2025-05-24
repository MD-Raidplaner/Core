<?php

namespace rp\data\item;

use rp\system\cache\builder\ItemCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit items.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method static   Item    create(array $parameters = [])
 * @method  Item    getDecoratedObject()
 * @mixin   Item
 */
class ItemEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    protected static $baseClass = Item::class;

    [\Override]
    public static function resetCache(): void
    {
        ItemCacheBuilder::getInstance()->reset();
    }
}
