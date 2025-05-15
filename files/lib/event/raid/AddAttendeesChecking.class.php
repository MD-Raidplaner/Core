<?php

namespace rp\event\raid;

use wcf\event\IPsr14Event;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class AddAttendeesChecking implements IPsr14Event
{
    /**
     *  attendees
     */
    private array $attendees = [];

    public function __construct(
        private readonly array $attendeeIDs,
    ) {}

    /**
     * Return a list of attendee ids
     */
    public function getAttendeeIDs(): array
    {
        return $this->attendeeIDs;
    }

    /**
     * Returns the attendees.
     */
    public function getAttendees(): array
    {
        return $this->attendees;
    }

    /**
     * Sets attendee.
     */
    public function setAttendee(array $attendee): void
    {
        $this->attendees[] = $attendee;
    }
}
