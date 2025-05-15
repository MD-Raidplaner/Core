<?php

use wcf\system\event\EventHandler;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */

return static function (): void {
    $eventHandler = EventHandler::getInstance();

    $eventHandler->register(
        \rp\event\character\profile\menu\CharacterProfileMenuCollecting::class,
        static function (\rp\event\character\profile\menu\CharacterProfileMenuCollecting $event) {
            $event->register(\rp\system\character\profile\menu\AboutCharacterProfileMenu::class, -100);
            $event->register(\rp\system\character\profile\menu\PointsCharacterProfileMenu::class, -75);
        }
    );

    $eventHandler->register(
        \wcf\event\endpoint\ControllerCollecting::class,
        static function (\wcf\event\endpoint\ControllerCollecting $event) {
            $event->register(new \wcf\system\endpoint\controller\rp\attendees\CreateAttendee);
            $event->register(new \wcf\system\endpoint\controller\rp\attendees\DeleteAttendee);
            $event->register(new \wcf\system\endpoint\controller\rp\attendees\RenderAttendee);
            $event->register(new \wcf\system\endpoint\controller\rp\attendees\UpdateAttendeeStatus);
            $event->register(new \wcf\system\endpoint\controller\rp\events\AvailableCharacters);
            $event->register(new \wcf\system\endpoint\controller\rp\events\CancelEvent);
            $event->register(new \wcf\system\endpoint\controller\rp\events\DeleteEvent);
            $event->register(new \wcf\system\endpoint\controller\rp\events\EnableDisableEvent);
            $event->register(new \wcf\system\endpoint\controller\rp\events\RestoreEvent);
            $event->register(new \wcf\system\endpoint\controller\rp\events\TrashEvent);
            $event->register(new \wcf\system\endpoint\controller\rp\items\Tooltip);
            $event->register(new \wcf\system\endpoint\controller\rp\items\SearchItem);
        }
    );
};
