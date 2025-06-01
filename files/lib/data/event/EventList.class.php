<?php

namespace rp\data\event;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Event   current()
 * @method  Event[] getObjects()
 * @method  Event|null  search($objectID)
 * @property    Event[] $objects
 */
class EventList extends DatabaseObjectList
{
    public $className = Event::class;
    public $sqlOrderBy = 'event.startTime';

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('event.game = ?', [\RP_CURRENT_GAME]);
    }
}
