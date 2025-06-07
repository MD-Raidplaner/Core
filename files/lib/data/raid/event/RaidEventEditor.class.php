<?php

namespace rp\data\raid\event;

use rp\system\cache\eager\PointAccountCache;
use rp\system\cache\eager\RaidEventCache;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;

/**
 * Provides functions to edit raid event.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @mixin   RaidEvent
 * @extends DatabaseObjectEditor<RaidEvent>
 */
class RaidEventEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    protected static $baseClass = RaidEvent::class;

    #[\Override]
    public static function resetCache(): void
    {
        (new RaidEventCache())->rebuild();
        (new PointAccountCache())->rebuild();
    }
}
