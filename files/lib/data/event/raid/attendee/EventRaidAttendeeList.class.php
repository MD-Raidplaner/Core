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
 * @extends DatabaseObjectList<EventRaidAttendee>
 */
class EventRaidAttendeeList extends DatabaseObjectList
{
    public $className = EventRaidAttendee::class;
}
