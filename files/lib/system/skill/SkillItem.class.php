<?php

namespace rp\system\skill;

use rp\system\game\GameHandler;
use rp\system\game\GameItem;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a skill item with a unique identifier.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class SkillItem
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $game,
        public readonly ?string $icon = null,
    ) {}

    /**
     * Returns the game object associated with this skill.
     */
    public function getGame(): GameItem
    {
        return GameHandler::getInstance()->getGameByIdentifier($this->game);
    }

    /**
     * Returns the HTML code to display the icon of the skill.
     */
    public function getIcon(int $size, string $type = ''): string
    {
        return \sprintf(
            '<img src="%s" style="width: %dpx; height: %dpx" alt="" class="skillIcon jsTooltip" title="%s" loading="lazy">',
            StringUtil::encodeHTML($this->getIconPath($type)),
            $size,
            $size,
            $this->getTitle()
        );
    }

    /**
     * Returns the full path to the icon.
     */
    public function getIconPath(string $type): string
    {
        $icon = ($this->icon ?? $this->identifier) . match ($type) {
            'female' => '_female',
            'male' => '_male',
            default => '',
        };

        return \sprintf(
            '%simages/%s/%s.webp',
            WCF::getPath('rp'),
            $this->getGame()->identifier,
            $icon
        );
    }

    /**
     * Returns the title of the skill.
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get(
            \sprintf(
                'rp.skill.%s.%s',
                $this->getGame()->identifier,
                $this->identifier
            )
        );
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
