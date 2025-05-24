<?php

namespace rp\system\cache\runtime;

use rp\data\character\Character;
use rp\data\character\CharacterList;
use wcf\system\cache\runtime\AbstractRuntimeCache;

/**
 * Runtime cache implementation for character profiles.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Character[]     getCachedObjects()
 * @method  Character|null  getObject($objectID)
 * @method  Character[]     getObjects(array $objectIDs)
 */
final class CharacterRuntimeCache extends AbstractRuntimeCache
{
    protected $listClassName = CharacterList::class;
}
