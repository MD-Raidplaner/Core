<?php

namespace rp\system\faction;

use rp\system\game\GameHandler;
use rp\system\game\GameItem;
use wcf\system\database\exception\DatabaseQueryExecutionException;
use wcf\system\exception\ClassNotFoundException;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a faction item with a unique identifier.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class FactionItem
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $game,
        public readonly ?string $icon = null,
    ) {}

    /**
     * Returns the game object associated with this faction.
     */
    public function getGame(): GameItem
    {
        return GameHandler::getInstance()->getGameByIdentifier($this->game);
    }

    /**
     * Returns the HTML code to display the icon of the faction.
     */
    public function getIcon(int $size): string
    {
        return \sprintf(
            '<img src="%s" style="width: %dpx; height: %dpx" alt="" class="factionIcon jsTooltip" title="%s" loading="lazy">',
            StringUtil::encodeHTML($this->getIconPath()),
            $size,
            $size,
            $this->getTitle()
        );
    }

    /**
     * Returns the full path to the icon.
     */
    public function getIconPath(): string
    {
        return \sprintf(
            '%simages/%s/%s.webp',
            WCF::getPath('rp'),
            $this->getGame()->identifier,
            $this->icon ?? $this->identifier
        );
    }

    /**
     * Returns the title of the faction.
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get(
            \sprintf(
                'rp.faction.%s.%s',
                $this->getGame()->identifier,
                $this->identifier
            )
        );
    }

    public function __toString()
    {
        $this->getTitle();
    }
}
