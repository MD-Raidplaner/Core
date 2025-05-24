<?php

namespace rp\data\faction;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of factions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Faction     current()
 * @method  Faction[]   getObjects()
 * @method  Faction|null    search($objectID)
 * @property    Faction[]   $objects
 */
class FactionList extends DatabaseObjectList
{
    public $className = Faction::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('faction.gameID = ?', [RP_CURRENT_GAME_ID]);
    }
}
