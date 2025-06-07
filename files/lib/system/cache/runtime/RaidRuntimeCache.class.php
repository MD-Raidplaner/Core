<?php

namespace rp\system\cache\runtime;

use rp\data\raid\RaidList;
use wcf\system\cache\runtime\AbstractRuntimeCache;

/**
 * Runtime cache implementation for events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  Raid[]  getCachedObjects()
 * @method  Raid|null   getObject($objectID)
 * @method  Raid[]  getObjects(array $objectIDs)
 */
final class RaidRuntimeCache extends AbstractRuntimeCache
{
    protected $listClassName = RaidList::class;
}
