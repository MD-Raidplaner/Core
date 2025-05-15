<?php

namespace rp\system\user\notification\event;

use rp\data\event\Event;
use rp\data\event\EventAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\UserProfile;
use wcf\system\WCF;

/**
 * Provides a method to create a event for testing user notification events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
trait TTestableEventUserNotificationEvent
{
    /**
     * Creates an test event.
     */
    public static function getTestEvent(UserProfile $author): Event
    {
        /** @var Event $event */
        return (new EventAction([], 'create', [
            'data' => [
                'additionalData' => \serialize([
                    'timezone' => WCF::getUser()->getTimeZone()->getName(),
                ]),
                'enableComments' => 1,
                'endTime' => TIME_NOW + (60 * 60 * 2),
                'notes' => 'Test Notes',
                'objectTypeID' => ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.md-raidplaner.rp.eventController', 'de.md-raidplaner.rp.event.default'),
                'startTime' => TIME_NOW,
                'title' => 'Test Event',
                'userID' => $author->userID,
                'username' => $author->username,
            ],
        ]))->executeAction()['returnValues'];
    }
}
