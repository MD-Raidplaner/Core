<?php

namespace rp\system\event\command;

use rp\data\event\Event;
use rp\data\event\EventAction;
use rp\event\event\EventEnabledDisabled;
use wcf\system\event\EventHandler;

/**
 * Enable/Disable a event.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class EnableDisableEvent
{
    public function __construct(
        private readonly Event $event,
        private readonly bool $isEnabled,
    ) {}

    public function __invoke(): void
    {
        $action = new EventAction(
            [$this->event],
            $this->isEnabled ? 'disable' : 'enable'
        );
        $action->executeAction();

        $event = new EventEnabledDisabled(new Event($this->event->eventID), !$this->isEnabled);
        EventHandler::getInstance()->fire($event);
    }
}
