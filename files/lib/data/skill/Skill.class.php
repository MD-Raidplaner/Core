<?php

namespace rp\data\skill;

use rp\data\game\Game;
use rp\system\cache\eager\GameCache;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a skill.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $skillID    unique id of the skill
 * @property-read   int $gameID     id of the game
 * @property-read   string  $identifier     unique textual identifier of the skill identifier
 * @property-read   string  $title      title of the skill or name of language item which contains the title
 * @property-read   string  $icon       icon of the skill
 * @property-read   int $isDisabled     is `1` if the skill is disabled and thus not selectable, otherwise `0`
 * @property-read   int $packageID      id of the package which delivers the skill
 */
final class Skill extends DatabaseObject implements ITitledObject
{
    /**
     * Returns game object.
     */
    public function getGame(): ?Game
    {
        return (new GameCache())->getCache()->getGame($this->gameID);
    }

    /**
     * Returns the html code to display the icon.
     */
    public function getIcon(int $size, string $type = ''): string
    {
        if (empty($this->icon)) return '';
        return '<img src="' . StringUtil::encodeHTML($this->getIconPath($type)) . '" style="width: ' . $size . 'px; height: ' . $size . 'px" alt="" class="gameIcon jsTooltip" title="' . $this->getTitle() . '" loading="lazy">';
    }

    /**
     * Returns full path to icon.
     */
    public function getIconPath(string $type): string
    {
        $filename = $this->icon;
        switch ($type) {
            case 'female':
                $filename .= '_female';
                break;
            case 'male':
                $filename .= '_male';
                break;
        }

        return WCF::getPath('rp') . 'images/' . $this->getGame()->identifier . '/' . $filename . '.png';
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
