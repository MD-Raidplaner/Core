<?php

namespace rp\system\event\command;

use rp\data\event\Event;
use rp\data\event\EventAction;
use rp\event\event\EventSoftDeleted;
use wcf\system\event\EventHandler;

/**
 * Soft delete an event.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class SoftDeleteEvent
{
    public function __construct(
        private readonly Event $event,
    ) {}

    public function __invoke(): void
    {
        $action = new EventAction(
            [$this->event],
            'trash'
        );
        $action->executeAction();

        $event = new EventSoftDeleted(new Event($this->event->eventID));
        EventHandler::getInstance()->fire($event);
    }
}
