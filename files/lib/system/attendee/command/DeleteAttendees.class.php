<?php

namespace rp\system\attendee\command;

use rp\data\event\raid\attendee\EventRaidAttendeeAction;
use rp\event\attendee\AttendeesDeleted;
use wcf\system\event\EventHandler;

/**
 * Deletes a bunch of attendees that belong to the same object type.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class DeleteAttendees
{
    private readonly array $attendeeIDs;

    public function __construct(
        private readonly array $attendees,
    ) {
        $this->attendeeIDs = \array_column($attendees, 'attendeeID');
    }

    public function __invoke(): void
    {
        $action = new EventRaidAttendeeAction($this->attendeeIDs, 'delete');
        $action->executeAction();

        $event = new AttendeesDeleted($this->attendees);
        EventHandler::getInstance()->fire($event);
    }
}
