<?php

namespace rp\system\event\command;

use rp\data\event\EventAction;
use rp\event\event\EventsDeleted;
use wcf\system\event\EventHandler;

/**
 * Deletes a bunch of events that belong to the same object type.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class DeleteEvents
{
    private readonly array $eventIDs;

    public function __construct(
        private readonly array $events,
    ) {
        $this->eventIDs = \array_column($events, 'eventID');
    }

    public function __invoke(): void
    {
        $action = new EventAction($this->eventIDs, 'delete');
        $action->executeAction();

        $event = new EventsDeleted($this->events);
        EventHandler::getInstance()->fire($event);
    }
}
