<?php

namespace rp\system\faction;

use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a faction.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class Faction
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $title = '',
        public readonly string $icon = '',
    ) {}

    /**
     * Return the HTML code for display the icon of the faction.
     */
    public function getIcon(int $size = 16): string
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
     * Returns the full path to the icon of the faction.
     */
    public function getIconPath(): string
    {
        return \sprintf(
            '%simages/faction/%s.webp',
            WCF::getPath('rp'),
            $this->icon ?: $this->identifier
        );
    }

    /**
     * Gets the title of the faction.
     */
    public function getTitle(): string
    {
        return $this->title ?: WCF::getLanguage()->get(\sprintf('rp.faction.%s', $this->identifier));
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}
