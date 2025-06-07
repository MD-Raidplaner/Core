<?php

namespace rp\data\event\raid\attendee;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit event raid attendee.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @mixin   EventRaidAttendee
 * @extends DatabaseObjectEditor<EventRaidAttendee>
 */
class EventRaidAttendeeEditor extends DatabaseObjectEditor
{
    protected static $baseClass = EventRaidAttendee::class;
}
