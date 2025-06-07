<?php

namespace rp\system\cache\runtime;

use rp\data\character\CharacterProfile;
use rp\data\character\CharacterProfileList;
use wcf\system\cache\runtime\AbstractRuntimeCache;

/**
 * Runtime cache implementation for character profiles.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  CharacterProfile[]  getCachedObjects()
 * @method  CharacterProfile|null   getObject($objectID)
 * @method  CharacterProfile[]  getObjects(array $objectIDs)
 */
final class CharacterProfileRuntimeCache extends AbstractRuntimeCache
{
    protected $listClassName = CharacterProfileList::class;
}
