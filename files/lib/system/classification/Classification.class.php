<?php

namespace rp\system\classification;

use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a classification.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class Classification
{
    /**
     * @param list<string> $factions
     * @param list<string> $races
     * @param list<string> $roles
     * @param list<string> $skills
     */
    public function __construct(
        public readonly string $identifier,
        public readonly string $title = '',
        public readonly string $icon = '',
        public readonly array $factions = [],
        public readonly array $races = [],
        public readonly array $roles = [],
        public readonly array $skills = [],
    ) {}

    /**
     * Return the HTML code for display the icon of the classification.
     */
    public function getIcon(int $size = 16, string $type = ''): string
    {
        return \sprintf(
            '<img src="%s" style="width: %dpx; height: %dpx" alt="" class="classificationIcon jsTooltip" title="%s" loading="lazy">',
            StringUtil::encodeHTML($this->getIconPath($type)),
            $size,
            $size,
            $this->getTitle()
        );
    }

    /**
     * Returns the full path to the icon of the classification.
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
            '%simages/classification/%s.webp',
            WCF::getPath('rp'),
            $icon
        );
    }

    /**
     * Gets the title of the classification.
     */
    public function getTitle(): string
    {
        return $this->title ?: WCF::getLanguage()->get(\sprintf('rp.classification.%s', $this->identifier));
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}
