<?php

namespace rp\system\log\modification;

use rp\data\event\Event;
use rp\data\event\EventList;
use rp\data\modification\log\ViewableEventModificationLog;
use wcf\data\modification\log\ModificationLog;
use wcf\system\log\modification\AbstractExtendedModificationLogHandler;

/**
 * Handles event modification logs.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventModificationLogHandler extends AbstractExtendedModificationLogHandler
{
    protected $objectTypeName = 'de.md-raidplaner.rp.event';

    /**
     * Adds a event modification log entry.
     */
    public function add(Event $event, string $action, array $additionalData = []): ModificationLog
    {
        return $this->createLog($action, $event->eventID, null, $additionalData);
    }

    /**
     * Adds a log entry for raid event cancel.
     */
    public function cancel(Event $event): void
    {
        $this->fireNotification($event, $this->add($event, 'cancel'));
    }

    /**
     * Adds a log entry for event delete.
     */
    public function delete(Event $event): void
    {
        $this->add($event, 'delete', [
            'time' => $event->created,
            'username' => $event->username,
        ]);
    }

    /**
     * Adds a log entry for event disabling.
     */
    public function disable(Event $event): void
    {
        $this->add($event, 'disable');
    }

    /**
     * Adds a log entry for event edit.
     */
    public function edit(Event $event): void
    {
        $this->add($event, 'edit');
    }

    /**
     * Adds a log entry for event enabling.
     */
    public function enable(Event $event): void
    {
        $this->fireNotification($event, $this->add($event, 'enable'));
    }

    /**
     * Fires a moderation notification.
     */
    protected function fireNotification(Event $event, ModificationLog $modificationLog): void
    {
        // TODO
    }

    #[\Override]
    public function getAvailableActions(): array
    {
        return [
            'cancel',
            'delete',
            'disable',
            'edit',
            'enable',
            'restore',
            'trash',
        ];
    }

    #[\Override]
    public function processItems(array $items): array
    {
        $eventIDs = \array_map(fn($item) => $item->objectID, $items);

        $eventList = new EventList();
        $eventList->setObjectIDs($eventIDs);
        $eventList->readObjects();
        $events = $eventList->getObjects();

        foreach ($items as &$item) {
            $item = new ViewableEventModificationLog($item);
            if (isset($events[$item->objectID])) {
                $item->setEvent($events[$item->objectID]);
            }
        }

        return $items;
    }

    /**
     * Adds a log entry for event restore.
     */
    public function restore(Event $event): void
    {
        $this->add($event, 'restore');
    }

    /**
     * Adds a log entry for event soft-delete (trash).
     */
    public function trash(Event $event): void
    {
        $this->fireNotification($event, $this->add($event, 'trash'));
    }
}
