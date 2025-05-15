<?php

namespace rp\data\character\avatar;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of avatars.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 *
 * @method  CharacterAvatar     current()
 * @method  CharacterAvatar[]       getObjects()
 * @method  CharacterAvatar|null    getSingleObject()
 * @method  CharacterAvatar|null    search($objectID)
 * @property    CharacterAvatar[]   $objects
 */
class CharacterAvatarList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = CharacterAvatar::class;
}
