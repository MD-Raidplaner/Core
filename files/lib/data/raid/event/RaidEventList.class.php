<?php

namespace rp\data\raid\event;

use wcf\data\DatabaseObjectList;

/**
 * Provides functions to edit raid event.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  RaidEvent       current()
 * @method  RaidEvent[]     getObjects()
 * @method  RaidEvent|null      search($objectID)
 * @property    RaidEvent[]     $objects
 */
class RaidEventList extends DatabaseObjectList
{
    public $className = RaidEvent::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('raid_event.game = ?', [\RP_CURRENT_GAME]);
    }
}
