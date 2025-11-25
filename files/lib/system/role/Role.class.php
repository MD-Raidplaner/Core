<?php

namespace rp\system\role;

use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a role.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class Role
{
    public function __construct(
        public readonly string $identifier,
        public readonly string $title = '',
        public readonly string $icon = ''
    ) {}

    /**
     * Return the HTML code for display the icon of the role.
     */
    public function getIcon(int $size = 16, string $type = ''): string
    {
        return \sprintf(
            '<img src="%s" style="width: %dpx; height: %dpx" alt="" class="roleIcon jsTooltip" title="%s" loading="lazy">',
            StringUtil::encodeHTML($this->getIconPath($type)),
            $size,
            $size,
            $this->getTitle()
        );
    }

    /**
     * Returns the full path to the icon of the role.
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
            '%simages/role/%s.webp',
            WCF::getPath('rp'),
            $icon
        );
    }

    /**
     * Gets the title of the role.
     */
    public function getTitle(): string
    {
        return $this->title ?: WCF::getLanguage()->get(\sprintf('rp.role.%s', $this->identifier));
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}
