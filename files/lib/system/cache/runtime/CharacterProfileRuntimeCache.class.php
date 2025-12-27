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
 * @extends AbstractRuntimeCache<CharacterProfile, CharacterProfileList>
 */
class CharacterProfileRuntimeCache extends AbstractRuntimeCache
{
    protected $listClassName = CharacterProfileList::class;

    /**
     * Adds the given character profile to this runtime cache.
     */
    public function addCharacterProfile(CharacterProfile $profile)
    {
        $objectID = $profile->getObjectID();

        if (!isset($this->objects[$objectID])) {
            $this->objects[$objectID] = $profile;
        }
    }
}
