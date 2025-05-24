<?php

namespace rp\data\event;

use rp\system\cache\runtime\EventRuntimeCache;
use rp\system\log\modification\EventModificationLogHandler;
use Symfony\Component\CssSelector\Parser\Handler\CommentHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\message\embedded\object\MessageEmbeddedObjectManager;
use wcf\system\user\activity\event\UserActivityEventHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * Executes event-related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  EventEditor[]   getObjects()
 * @method  EventEditor     getSingleObject()
 */
class EventAction extends AbstractDatabaseObjectAction
{
    protected ?Event $event = null;

    public function appointmentSetStatus(): array
    {
        $additionalData = $this->event->additionalData;
        $appointments = $additionalData['appointments'] ?? ['accepted' => [], 'canceled' => [], 'maybe' => []];

        $appointments = \array_map(
            fn($users) => \array_filter($users, fn($userID) => $userID !== WCF::getUser()->userID),
            $appointments
        );

        $appointments[$this->parameters['status']][] = WCF::getUser()->userID;
        $additionalData['appointments'] = $appointments;

        $action = new self([$this->event], 'update', ['data' => [
            'additionalData' => \serialize($additionalData)
        ]]);
        $action->executeAction();

        return [
            'template' => WCF::getTPL()->fetch(
                'userListItem',
                'rp',
                [
                    'user' => new UserProfile(WCF::getUser()),
                ]
            ),
        ];
    }

    /**
     * Cancel raid events.
     */
    public function cancel(): void
    {
        foreach ($this->getObjects() as $event) {
            if ($event->isCanceled) {
                continue;
            }

            $event->update([
                'isCanceled' => 1,
            ]);

            EventModificationLogHandler::getInstance()->cancel($event->getDecoratedObject());
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    #[\Override]
    public function create(): Event
    {
        $this->parameters['data']['userID'] ??= WCF::getUser()->userID;
        $this->parameters['data']['username'] ??= WCF::getUser()->username;
        $this->parameters['data']['isDisabled'] = WCF::getSession()->getPermission('user.rp.canCreateEventWithoutModeration') ? 0 : 1;
        $this->parameters['data']['created'] = TIME_NOW;
        $this->parameters['data']['gameID'] ??= RP_CURRENT_GAME_ID;

        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            $this->parameters['data']['notes'] = $this->parameters['notes_htmlInputProcessor']->getHtml();
        }

        /** @var Event $event */
        $event = parent::create();
        $eventEditor = new EventEditor($event);

        // save embedded objects
        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            /** @noinspection PhpUndefinedMethodInspection */
            $this->parameters['notes_htmlInputProcessor']->setObjectID($event->eventID);
            if (MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['notes_htmlInputProcessor'])) {
                $eventEditor->update(['hasEmbeddedObjects' => 1]);
            }
        }

        if (!$event->isDisabled) {
            $action = new EventAction([$eventEditor], 'triggerPublication');
            $action->executeAction();
        }

        return new Event($event->eventID);
    }

    #[\Override]
    public function delete(): void
    {
        $eventIDs = [];
        foreach ($this->getObjects() as $event) {
            $eventIDs[] = $event->eventID;

            EventModificationLogHandler::getInstance()->delete($event->getDecoratedObject());
        }

        parent::delete();

        if (!empty($eventIDs)) {
            // delete comments
            CommentHandler::getInstance()->deleteObjects('de.md-raidplaner.rp.eventComment', $eventIDs);

            // delete embedded object references
            MessageEmbeddedObjectManager::getInstance()->removeObjects('de.md-raidplaner.rp.event.notes', $eventIDs);

            // delete modification log entries except for deleting the events
            EventModificationLogHandler::getInstance()->deleteLogs($eventIDs, ['delete']);

            // delete recent activity events
            UserActivityEventHandler::getInstance()->removeEvents(
                'de.md-raidplaner.rp.event.recentActivityEvent',
                $eventIDs
            );
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * Disables events.
     */
    public function disable(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        $eventIDs = [];
        foreach ($this->getObjects() as $event) {
            $event->update([
                'isDisabled' => 1
            ]);

            EventModificationLogHandler::getInstance()->disable($event->getDecoratedObject());

            $eventIDs[] = $event->getObjectID;
        }

        // delete recent activity events
        UserActivityEventHandler::getInstance()->removeEvents(
            'de.md-raidplaner.rp.event.recentActivityEvent',
            $eventIDs
        );

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * Enables events.
     */
    public function enable(): void
    {
        $eventIDs = [];
        foreach ($this->getObjects() as $event) {
            $eventIDs[] = $event->eventID;

            $event->update([
                'isDisabled' => 0
            ]);

            UserActivityEventHandler::getInstance()->fireEvent(
                'de.md-raidplaner.rp.event.recentActivityEvent',
                $event->getObjectID(),
                null,
                $event->getUserID(),
                TIME_NOW
            );

            EventModificationLogHandler::getInstance()->enable($event->getDecoratedObject());
        }

        // trigger publication
        if (!empty($eventIDs)) {
            $action = new EventAction($eventIDs, 'triggerPublication');
            $action->executeAction();
        }
    }

    /**
     * Restores events.
     */
    public function restore(): void
    {
        foreach ($this->getObjects() as $event) {
            if (!$event->isDeleted) {
                continue;
            }

            $event->update([
                'deleteTime' => 0,
                'isDeleted' => 0,
            ]);

            EventModificationLogHandler::getInstance()->restore($event->getDecoratedObject());
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * Trashes events.
     */
    public function trash(): void
    {
        foreach ($this->getObjects() as $event) {
            if ($event->isDeleted) {
                continue;
            }

            $event->update([
                'deleteTime' => TIME_NOW,
                'isDeleted' => 1,
            ]);

            EventModificationLogHandler::getInstance()->trash($event->getDecoratedObject());
        }

        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * Triggers the publication of events.
     */
    public function triggerPublication(): void
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $event) {
            UserActivityEventHandler::getInstance()->fireEvent(
                'de.md-raidplaner.rp.event.recentActivityEvent',
                $event->getObjectID(),
                null,
                $event->getUserID(),
                $event->getTime()
            );
        }
        // reset storage
        UserStorageHandler::getInstance()->resetAll('rpUnreadEvents');
    }

    /**
     * Marks events as read.
     */
    public function markAsRead(): void
    {
        $this->parameters['visitTime'] ??= TIME_NOW;

        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $event) {
            VisitTracker::getInstance()->trackObjectVisit(
                'de.md-raidplaner.rp.event',
                $event->eventID,
                $this->parameters['visitTime']
            );
        }

        // reset storage
        if (WCF::getUser()->userID) {
            UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'rpUnreadEvents');
        }
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        if (!empty($this->parameters['notes_htmlInputProcessor'])) {
            $this->parameters['data']['notes'] = $this->parameters['notes_htmlInputProcessor']->getHtml();
        }

        parent::update();

        $isDisabled = $this->parameters['data']['isDisabled'] ?? null;
        foreach ($this->getObjects() as $event) {
            // save embedded objects
            if (!empty($this->parameters['notes_htmlInputProcessor'])) {
                /** @noinspection PhpUndefinedMethodInspection */
                $this->parameters['notes_htmlInputProcessor']->setObjectID($event->eventID);
                if ($event->hasEmbeddedObjects != MessageEmbeddedObjectManager::getInstance()->registerObjects($this->parameters['notes_htmlInputProcessor'])) {
                    $event->update(['hasEmbeddedObjects' => $event->hasEmbeddedObjects ? 0 : 1]);
                }
            }

            $resetEventIDs = [];
            if (
                $isDisabled !== null &&
                $isDisabled != $event->isDisabled
            ) {
                if (!$isDisabled) {
                    UserActivityEventHandler::getInstance()->fireEvent(
                        'de.md-raidplaner.rp.event.recentActivityEvent',
                        $event->getObjectID(),
                        null,
                        $this->parameters['data']['userID'] ?? $event->getUserID(),
                        $this->parameters['data']['created'] ?? $event->getTime()
                    );
                } else {
                    $resetEventIDs[] = $event->eventID;
                }
            }

            if (!empty($resetEventIDs)) {
                // delete recent activity events
                UserActivityEventHandler::getInstance()->removeEvents(
                    'de.md-raidplaner.rp.event.recentActivityEvent',
                    $resetEventIDs
                );
            }


            EventModificationLogHandler::getInstance()->edit($event->getDecoratedObject());
        }
    }

    public function validateAppointmentSetStatus(): void
    {
        $this->readInteger('eventID');
        $this->readString('status');

        $this->event = EventRuntimeCache::getInstance()->getObject($this->parameters['eventID']);
        if ($this->event === null) {
            throw new UserInputException('eventID');
        }

        if ($this->event->objectTypeID !== ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.md-raidplaner.rp.event.controller', 'de.md-raidplaner.rp.event.controller.appointment')) {
            throw new PermissionDeniedException();
        }

        if (!$this->event->canRead()) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Validates parameters to cancel events.
     */
    public function validateCancel(): void
    {
        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $event) {
            if (!$event->canCancel()) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * Validates the disable action.
     */
    public function validateDisable()
    {
        $this->validateEnable();
    }

    /**
     * Validates the enable action.
     */
    public function validateEnable(): void
    {
        WCF::getSession()->checkPermissions(['mod.rp.canEditEvent']);

        if (empty($this->objects)) {
            $this->readObjects();
        }
    }

    /**
     * Validates the mark all as read action.
     */
    public function validateMarkAllAsRead(): void
    {
        // does nothing
    }

    /**
     * Validates parameters to restore events.
     */
    public function validateRestore(): void
    {
        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $event) {
            if (!$event->canRestore()) {
                throw new PermissionDeniedException();
            }
        }
    }

    /**
     * Validates parameters to trash events.
     */
    public function validateTrash(): void
    {
        // read objects
        if (empty($this->objects)) {
            $this->readObjects();

            if (empty($this->objects)) {
                throw new UserInputException('objectIDs');
            }
        }

        foreach ($this->getObjects() as $event) {
            if (!$event->canTrash()) {
                throw new PermissionDeniedException();
            }
        }
    }

    public function validateUpdate(): void
    {
        parent::validateUpdate();

        $this->readBoolean('noLog', true);
    }
}
