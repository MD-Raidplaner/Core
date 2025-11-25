<?php

namespace rp\system\race;

use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a race.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class Race
{
    /**
     * @param list<string> $factions
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $title = '',
        public readonly string $icon = '',
        public readonly array $factions = []
    ) {}

    /**
     * Return the HTML code for display the icon of the race.
     */
    public function getIcon(int $size = 16, string $type = ''): string
    {
        return \sprintf(
            '<img src="%s" style="width: %dpx; height: %dpx" alt="" class="raceIcon jsTooltip" title="%s" loading="lazy">',
            StringUtil::encodeHTML($this->getIconPath($type)),
            $size,
            $size,
            $this->getTitle()
        );
    }

    /**
     * Returns the full path to the icon of the race.
     */
    public function getIconPath(string $type = ''): string
    {
        $icon = \sprintf(
            '%s%s',
            $this->icon ?: $this->identifier,
            match ($type) {
                'female' => '_female',
                'male' => '_male',
                default => '',
            }
        );

        return \sprintf(
            '%simages/race/%s.webp',
            WCF::getPath('rp'),
            $icon
        );
    }

    /**
     * Returns the title of the race.
     */
    public function getTitle(): string
    {
        return $this->title ?: WCF::getLanguage()->get(\sprintf('rp.race.%s', $this->identifier));
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}
