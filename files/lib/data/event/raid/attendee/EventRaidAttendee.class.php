<?php

namespace rp\data\event\raid\attendee;

use rp\data\character\Character;
use rp\data\character\CharacterProfile;
use rp\data\event\Event;
use rp\system\cache\runtime\CharacterProfileRuntimeCache;
use rp\system\cache\runtime\EventRuntimeCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledLinkObject;
use wcf\system\WCF;

/**
 * Represents a event raid attendee.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $attendeeID     unique id of the attendee
 * @property-read   int $eventID        id of the event
 * @property-read   int $characterID        id of the character or `null` for a guest registration
 * @property-read   int $characterName      character name
 * @property-read   string  $email      email address of the participant for a guest registration
 * @property-read   string  $internID       special id for the character of the attendee
 * @property-read   int $classificationID       id of the classification
 * @property-read   int $roleID     id of the role
 * @property-read   string  $notes      notes of the attendee
 * @property-read   int $created        timestamp at which the attendee has been created
 * @property-read   int $addByLeader        is `1` if the attendee added by raid leader, otherwise `0`
 * @property-read   int $status     status of the raid attendee (see `EventRaidAttendee::STATUS_*` constants)
 */
final class EventRaidAttendee extends DatabaseObject implements ITitledLinkObject
{
    // states of column 'status'
    const STATUS_LOGIN = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_LOGOUT = 2;
    const STATUS_RESERVE = 3;

    protected ?CharacterProfile $character = null;
    protected ?Event $event = null;

    /**
     * @var mixed[]
     */
    protected ?array $possibleDistribution = null;

    /**
     * Returns the character profile of the current attendee.
     */
    public function getCharacter(): ?CharacterProfile
    {
        if ($this->character === null) {
            $this->character = CharacterProfileRuntimeCache::getInstance()->getObject($this->characterID)
                ?? new CharacterProfile(
                    new Character(
                        null,
                        [
                            'characterName' => $this->characterName,
                            'created' => $this->created,
                            'isPrimary' => 1,
                        ]
                    )
                );
        }

        return $this->character;
    }

    /**
     * Returns the event of the current attendee.
     */
    public function getEvent(): Event
    {
        return $this->event ??= EventRuntimeCache::getInstance()->getObject($this->eventID);
    }

    [\Override]
    public function getLink(): string
    {
        return $this->getCharacter()->getLink();
    }

    /**
     * Returns the possible distribution of the current attendee.
     * 
     * @return  mixed[]
     */
    public function getPossibleDistribution(): array
    {
        if ($this->possibleDistribution === null) {
            $this->possibleDistribution = [];

            switch ($this->getEvent()->distributionMode) {
                case 'class':
                    $this->possibleDistribution[] = $this->classificationID;
                    break;
                case 'none':
                    $this->possibleDistribution[] = 'none';
                    break;
                case 'role':
                    $sql = "SELECT  roleID
                            FROM    rp1_classification_to_role
                            WHERE   classificationID = ?";
                    $statement = WCF::getDB()->prepare($sql);
                    $statement->execute([$this->classificationID]);
                    $this->possibleDistribution = $statement->fetchAll(\PDO::FETCH_COLUMN);
                    break;
            }
        }

        return $this->possibleDistribution;
    }

    [\Override]
    public function getTitle(): string
    {
        return $this->characterName;
    }

    [\Override]
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
