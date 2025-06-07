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
 * @extends DatabaseObjectList<Raid>
 */
class RaidList extends DatabaseObjectList
{
    public $className = Raid::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('raid.game = ?', [\RP_CURRENT_GAME]);
    }
}
