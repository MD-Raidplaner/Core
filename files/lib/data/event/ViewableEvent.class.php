<?php

namespace rp\data\event;

use rp\data\modification\log\ViewableEventModificationLog;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\visitTracker\VisitTracker;
use wcf\system\WCF;

/**
 * Represents a viewable event.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Event   getDecoratedObject()
 * @mixin   Event
 */
class ViewableEvent extends DatabaseObjectDecorator
{
    protected static $baseClass = Event::class;
    protected ?int $effectiveVisitTime = null;
    protected ?array $eventDays = null;
    protected ?ViewableEventModificationLog $logEntry = null;
    protected static ?int $unreadEvents = null;
    protected ?UserProfile $userProfile = null;

    /**
     * Returns true if this user can edit his event.
     */
    public function canEditOwnEvent(): bool
    {
        if (
            $this->userID &&
            $this->userID == WCF::getUser()->userID &&
            WCF::getSession()->getPermission('user.rp.canEditOwnEvent')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns delete note if applicable.
     */
    public function getDeleteNote(): string
    {
        if ($this->logEntry === null || $this->logEntry->action != 'trash') {
            return '';
        }

        return WCF::getLanguage()->getDynamicVariable('rp.event.deleted', ['event' => $this]);
    }

    /**
     * Returns a specific event decorated as viewable event or `null` if it does not exist.
     */
    public static function getEvent(int $eventID): ?ViewableEvent
    {
        $list = new ViewableEventList();
        $list->setObjectIDs([$eventID]);
        $list->readObjects();

        return $list->getSingleObject();
    }

    /**
     * Returns an array of days in the specified month on what day the event occurs.
     * 
     * The returned array has the format:
     * [
     * month1 => [tag1, tag2, ...],
     * month2 => [tag3, tag4, ...],
     * ...
     * ]
     */
    public function getEventDays(int $month): array
    {
        if ($this->eventDays === null) {
            $this->eventDays = [];

            $startDateTime = new \DateTimeImmutable('@' . $this->startTime, !$this->isFullDay ? WCF::getUser()->getTimeZone() : null);
            $endDateTime = new \DateTimeImmutable('@' . $this->endTime, !$this->isFullDay ? WCF::getUser()->getTimeZone() : null);

            for ($day = $startDateTime; $day <= $endDateTime; $day = $day->modify('+1 day')) {
                $this->eventDays[$day->format('n')][] = $day->format('Y-m-d');
            }

            $this->eventDays[$endDateTime->format('n')] ??= [];
            if (!\in_array($endDateTime->format('Y-m-d'), $this->eventDays[$endDateTime->format('n')])) {
                $this->eventDays[$endDateTime->format('n')][] = $endDateTime->format('Y-m-d');
            }
        }

        return $this->eventDays[$month] ?? [];
    }

    /**
     * Returns modification log entry.
     */
    public function getLogEntry(): ViewableEventModificationLog
    {
        return $this->logEntry;
    }

    /**
     * Returns the number of unseen events.
     */
    public static function getUnreadEvents(): int
    {
        if (self::$unreadEvents === null) {
            self::$unreadEvents = 0;

            if (WCF::getUser()->userID) {
                $unreadEvents = UserStorageHandler::getInstance()->getField('rpUnreadEvents');

                // cache does not exist or is outdated
                if ($unreadEvents === null) {
                    $conditionBuilder = new PreparedStatementConditionBuilder();
                    $conditionBuilder->add(
                        'event.created > ?',
                        [VisitTracker::getInstance()->getVisitTime('de.md-raidplaner.rp.event')]
                    );
                    $conditionBuilder->add('(event.created > tracked_visit.visitTime OR tracked_visit.visitTime IS NULL)');
                    $conditionBuilder->add('event.isDeleted = ?', [0]);
                    $conditionBuilder->add('event.game = ?', [\RP_CURRENT_GAME]);

                    $sql = "SELECT      COUNT(*)
                            FROM        rp1_event event
                            LEFT JOIN   wcf1_tracked_visit tracked_visit
                            ON          tracked_visit.objectTypeID = " . VisitTracker::getInstance()->getObjectTypeID('de.md-raidplaner.rp.event') . "
                                    AND tracked_visit.objectID = event.eventID
                                    AND tracked_visit.userID = " . WCF::getUser()->userID . "
                            " . $conditionBuilder;
                    $statement = WCF::getDB()->prepare($sql);
                    $statement->execute($conditionBuilder->getParameters());
                    self::$unreadEvents = $statement->fetchSingleColumn();

                    // update storage unread events
                    UserStorageHandler::getInstance()->update(
                        WCF::getUser()->userID,
                        'rpUnreadEvents',
                        self::$unreadEvents
                    );
                } else {
                    self::$unreadEvents = $unreadEvents;
                }
            }
        }

        return self::$unreadEvents;
    }

    /**
     * Returns the user profile object.
     */
    public function getUserProfile(): UserProfile
    {
        if ($this->userProfile === null) {
            if ($this->userID) {
                $this->userProfile = UserProfileRuntimeCache::getInstance()->getObject($this->userID);
            } else {
                $this->userProfile = UserProfile::getGuestUserProfile($this->username);
            }
        }

        return $this->userProfile;
    }

    /**
     * Returns the effective visit time.
     */
    public function getVisitTime(): int
    {
        if ($this->effectiveVisitTime === null) {
            if (WCF::getUser()->userID) {
                $this->effectiveVisitTime = \max(
                    $this->visitTime,
                    VisitTracker::getInstance()->getVisitTime('de.md-raidplaner.rp.event')
                );
            } else {
                $this->effectiveVisitTime = \max(
                    VisitTracker::getInstance()->getObjectVisitTime(
                        'de.md-raidplaner.rp.event',
                        $this->eventID
                    ),
                    VisitTracker::getInstance()->getVisitTime('de.md-raidplaner.rp.event')
                );
            }
            if ($this->effectiveVisitTime === null) {
                $this->effectiveVisitTime = 0;
            }
        }

        return $this->effectiveVisitTime;
    }

    /**
     * Returns true if this event is new for the active user.
     */
    public function isNew(): bool
    {
        return $this->created > $this->getVisitTime();
    }

    /**
     * Sets modification log entry.
     */
    public function setLogEntry(ViewableEventModificationLog $logEntry): void
    {
        $this->logEntry = $logEntry;
    }
}
