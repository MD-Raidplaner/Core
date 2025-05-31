<?php

namespace rp\data\character;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Character   current()
 * @method  Character[] getObjects()
 * @method  Character|null  search($objectID)
 * @property    Character[] $objects
 */
class CharacterList extends DatabaseObjectList
{
    public $className = Character::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('member.game = ?', [\RP_CURRENT_GAME]);
    }
}
