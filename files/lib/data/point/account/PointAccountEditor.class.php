<?php

namespace rp\data\point\account;

use rp\system\cache\eager\PointAccountCache;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit point account.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  PointAccount
 * @extends DatabaseObjectEditor<PointAccount>
 */
class PointAccountEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    protected static $baseClass = PointAccount::class;

    #[\Override]
    public static function resetCache(): void
    {
        (new PointAccountCache())->rebuild();
    }
}
