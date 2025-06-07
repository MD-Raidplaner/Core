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
 * @extends DatabaseObjectList<CharacterAvatar>
 */
class CharacterAvatarList extends DatabaseObjectList
{
    public $className = CharacterAvatar::class;
}
