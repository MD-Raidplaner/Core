<?php

namespace rp\data\raid;

use rp\data\raid\event\RaidEvent;
use rp\data\raid\event\RaidEventCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledLinkObject;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Represents a raid.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $raidID     unique id of the raid
 * @property-read   int $raidEventID        raid event id of the raid
 * @property-read   int $time       timestamp at which the raid has been created
 * @property-read   string  $addedBy        who added the raid
 * @property-read   string  $updatedBy      who updated the raid
 * @property-read   float   $points     points of the raid
 * @property-read   string  $notes      notes of the raid
 */
final class Raid extends DatabaseObject implements IRouteController, ITitledLinkObject
{
    protected ?array $attendees = null;
    protected ?RaidEvent $raidEvent = null;

    /**
     * Returns the attendees of the raid.
     */
    public function getAttendees(): array
    {
        if ($this->attendees === null) {
            $this->attendees = [];

            $sql = "SELECT  *
                    FROM    rp1_raid_attendee
                    WHERE   raidID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->raidID]);

            while ($row = $statement->fetchArray()) {
                $this->attendees[] = $row;
            }
        }

        return $this->attendees;
    }

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(int $size): string
    {
        return $this->getRaidEvent()->getIcon($size);
    }

    #[\Override]
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink('Raid', [
            'application' => 'rp',
            'forceFrontend' => true,
            'object' => $this
        ]);
    }

    /**
     * Returns the raid event with the given raid event id.
     */
    public function getRaidEvent()
    {
        if ($this->raidEvent === null) {
            $this->raidEvent = RaidEventCache::getInstance()->getEventByID($this->raidEventID);
        }

        return $this->raidEvent;
    }

    #[\Override]
    public function getTitle(): string
    {
        return $this->getRaidEvent()->getTitle();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
