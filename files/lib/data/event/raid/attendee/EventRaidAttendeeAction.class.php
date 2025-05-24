<?php

namespace rp\data\event\raid\attendee;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes event raid attendee related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  EventRaidAttendeeEditor[]   getObjects()
 * @method  EventRaidAttendeeEditor     getSingleObject()
 */
class EventRaidAttendeeAction extends AbstractDatabaseObjectAction
{
    [\Override]
    public function create(): EventRaidAttendee
    {
        $this->parameters['data']['created'] = TIME_NOW;

        return parent::create();
    }
}
