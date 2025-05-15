<?php

namespace rp\event\attendee;

use rp\data\event\raid\attendee\EventRaidAttendee;
use wcf\event\IPsr14Event;

/**
 * Indicates that a new attendee has been created.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AttendeeCreated implements IPsr14Event
{
    public function __construct(
        public readonly EventRaidAttendee $attendee,
    ) {
    }
}
