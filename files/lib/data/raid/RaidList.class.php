<?php

namespace rp\data\raid;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of raids.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Raid    current()
 * @method  Raid[]  getObjects()
 * @method  Raid|null   search($objectID)
 * @property    Raid[]  $objects
 */
class RaidList extends DatabaseObjectList
{
    public $className = Raid::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('raid.gameID = ?', [RP_CURRENT_GAME_ID]);
    }
}
