<?php

namespace rp\data\race;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of races.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Race        current()
 * @method  Race[]      getObjects()
 * @method  Race|null   search($objectID)
 * @property    Race[]  $objects
 */
class RaceList extends DatabaseObjectList
{
    public $className = Race::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('race.gameID = ?', [RP_CURRENT_GAME_ID]);
    }
}
