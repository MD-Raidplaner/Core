<?php

namespace rp\data\raid\event;

use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountCache;
use rp\system\cache\eager\GameCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledLinkObject;
use wcf\data\ITitledObject;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a raid event.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $eventID        unique id of the raid event
 * @property-read   string  $title      title of the race or name of language item which contains the title
 * @property-read   int|null    $pointAccountID     id of the point account, or `null` if not assigned
 * @property-read   int $gameID     id of the game
 * @property-read   float   $defaultPoints      default points of the raid event
 * @property-read   string  $icon       icon of the raid event
 * @property-read   int $showProfile        is `1` if the raid event is show in profile, otherwise `0`
 */
final class RaidEvent extends DatabaseObject implements ITitledLinkObject
{
    protected ?PointAccount $pointAccount = null;

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(?int $size): string
    {
        if ($size === null) $size = $this->size;

        return \sprintf(
            '<img src="%s"" style="width: %dpx; height: %dpx;" alt="" class="raidEventIcon">',
            StringUtil::encodeHTML($this->getIconPath()),
            $size,
            $size
        );
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(): string
    {
        return \sprintf(
            '%simages/%s/%s.png',
            WCF::getPath('rp'),
            (new GameCache())->getCache()->getGame($this->gameID)->identifier,
            $this->icon ?? 'unknown'
        );
    }

    #[\Override]
    public function getLink(): string
    {
        return LinkHandler::getInstance()->getLink(
            'RaidList',
            [
                'application' => 'rp',
                'forceFrontend' => true
            ],
            \sprintf(
                'raidEventID=%d',
                $this->eventID
            )
        );
    }

    /**
     * Returns the point account with the given point account id or 
     * `null` if no such point account exists.
     */
    public function getPointAccount(): ?PointAccount
    {
        if ($this->pointAccount === null) {
            $this->pointAccount = PointAccountCache::getInstance()->getAccountByID($this->pointAccountID);
        }

        return $this->pointAccount;
    }

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
