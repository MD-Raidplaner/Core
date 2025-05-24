<?php

namespace rp\data\point\account;

use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;

/**
 * Represents a point account.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $accountID      unique id of the point account
 * @property-read   string  $title      title of the point account or name of language item which contains the title
 * @property-read   string  $description        description of the point account or name of language item which contains the description
 * @property-read   int $gameID     id of the game
 */
final class PointAccount extends DatabaseObject implements ITitledObject
{
    #[\Override]
    public function getTitle(): string
    {
        return WCF::getLanguage()->get($this->title);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
