<?php

namespace rp\data\character;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @template-covariant TDatabaseObject of Character|DatabaseObjectDecorator<Character> = Character
 * @extends DatabaseObjectList<TDatabaseObject>
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
