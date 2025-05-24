<?php

namespace rp\data\event\raid\attendee;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of event raid attendees.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  EventRaidAttendee   current()
 * @method  EventRaidAttendee[] getObjects()
 * @method  EventRaidAttendee|null  search($objectID)
 * @property    EventRaidAttendee[] $objects
 */
class EventRaidAttendeeList extends DatabaseObjectList
{
    public $className = EventRaidAttendee::class;
}
