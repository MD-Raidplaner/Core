<?php

namespace rp\data\raid;

use rp\data\event\Event;
use rp\data\event\raid\attendee\EventRaidAttendee;
use rp\data\event\raid\attendee\EventRaidAttendeeList;
use rp\event\raid\AddAttendeesChecking;
use rp\system\cache\builder\CharacterPointCacheBuilder;
use rp\system\cache\builder\RaidStatsCacheBuilder;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\event\EventHandler;
use wcf\system\WCF;

/**
 * Provides functions to edit raid.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method static   Raid    create(array $parameters = [])
 * @method  Raid    getDecoratedObject()
 * @mixin   Raid
 */
class RaidEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    protected static $baseClass = Raid::class;

    /**
     * Adds attendees to the raid.
     */
    public function addAttendees(array $attendeeIDs, bool $deleteOldAttendees = true, ?Event $event = null): void
    {
        // remove old attendees
        if ($deleteOldAttendees) {
            $sql = "DELETE FROM rp1_raid_attendee
                    WHERE       raidID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->raidID]);
        }

        // insert new attendees
        $attendees = [];
        if ($event !== null) {
            $attendeeList = new EventRaidAttendeeList();
            $attendeeList->getConditionBuilder()->add('eventID = ?', [$event->eventID]);
            $attendeeList->getConditionBuilder()->add('status = ?', [EventRaidAttendee::STATUS_CONFIRMED]);
            $attendeeList->readObjects();

            foreach ($attendeeList as $attendee) {
                if ($attendee->characterID === null) continue;

                $attendees[] = [
                    'characterID' => $attendee->characterID,
                    'characterName' => $attendee->characterName,
                    'classificationID' => $attendee->classificationID,
                    'roleID' => $attendee->roleID,
                ];
            }
        } else {
            $event = new AddAttendeesChecking(
                $attendeeIDs,
            );
            EventHandler::getInstance()->fire($event);

            $attendees = $event->getAttendees();
        }

        if (empty($attendees)) return;

        $sql = "INSERT IGNORE INTO  rp1_raid_attendee
                                    (raidID, characterID, characterName, classificationID, roleID)
                VALUES              (?, ?, ?, ?, ?)";
        $statement = WCF::getDB()->prepare($sql);
        WCF::getDB()->beginTransaction();
        foreach ($attendees as $attendee) {
            $statement->execute([
                $this->raidID,
                $attendee['characterID'],
                $attendee['characterName'],
                $attendee['classificationID'],
                $attendee['roleID'],
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * Adds items to the raid.
     */
    public function addItems(array $items, bool $deleteOldItems = true): void
    {
        // remove old items
        if ($deleteOldItems) {
            $sql = "DELETE FROM rp1_item_to_raid
                    WHERE       raidID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->raidID]);
        }

        // insert new items
        if (!empty($items)) {
            $sql = "INSERT IGNORE INTO  rp1_item_to_raid
                                        (itemID, characterID, raidID, pointAccountID, points)
                    VALUES              (?, ?, ?, ?, ?)";
            $statement = WCF::getDB()->prepare($sql);
            foreach ($items as $item) {
                $statement->execute([
                    $item['itemID'],
                    $item['characterID'],
                    $this->raidID,
                    $item['pointAccountID'],
                    $item['points'],
                ]);
            }
        }
    }

    #[\Override]
    public static function resetCache()
    {
        CharacterPointCacheBuilder::getInstance()->reset();
        RaidStatsCacheBuilder::getInstance()->reset();
    }
}
