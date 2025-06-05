<?php

namespace rp\system\attendee\command;

use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeAction;
use rp\event\attendee\AttendeeUpdated;
use wcf\system\event\EventHandler;

/**
 * Updates a attendee.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class UpdateAttendeeStatus
{
    public function __construct(
        private readonly EventRaidAttendee $attendee,
        private readonly string $role,
        /** @var int<0, max> */
        private readonly int $status,
    ) {
    }

    public function __invoke(): void
    {
        $data = [
            'role' => $this->role,
            'status' => $this->status,
        ];

        $action = new EventRaidAttendeeAction([$this->attendee], 'update', [
            'data' => $data,
        ]);
        $action->executeAction();

        $event = new AttendeeUpdated(new EventRaidAttendee($this->attendee->attendeeID));
        EventHandler::getInstance()->fire($event);
    }
}
