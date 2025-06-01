<?php

namespace rp\system\attendee\command;

use rp\data\event\Event;
use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeAction;
use rp\event\attendee\AttendeeCreated;
use wcf\system\event\EventHandler;

/**
 * Creates a new attendee.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CreateAttendee
{
    public function __construct(
        private readonly Event $event,
        private readonly int $characterID,
        private readonly string $characterName,
        private readonly int $classificationID,
        private readonly int|string $internID,
        private readonly string $role,
        private readonly int $status,
    ) {
    }

    public function __invoke(): EventRaidAttendee
    {
        $action = new EventRaidAttendeeAction([], 'create', [
            'data' => [
                'characterID' => $this->characterID,
                'characterName' => $this->characterName,
                'classificationID' => $this->classificationID,
                'eventID' => $this->event->eventID,
                'internID' => $this->internID,
                'role' => $this->role,
                'status' => $this->status,
            ]
        ]);
        $attendee = $action->executeAction()['returnValues'];
        \assert($attendee instanceof EventRaidAttendee);

        $event = new AttendeeCreated($attendee);
        EventHandler::getInstance()->fire($event);

        return $attendee;
    }
}
