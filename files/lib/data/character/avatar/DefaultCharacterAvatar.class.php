<?php

namespace rp\data\character\avatar;

use rp\system\file\processor\CharacterAvatarFileProcessor;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Any displayable avatar type should implement this class.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
class DefaultCharacterAvatar implements ICharacterAvatar, ISafeFormatAvatar
{
    /**
     * image size
     */
    public int $size = CharacterAvatarFileProcessor::AVATAR_SIZE;

    /**
     * content of the `src` attribute
     */
    protected string $src = '';

    public function __construct(string $characterName = '')
    {
        if (\defined('AVATAR_DEFAULT_TYPE') && \AVATAR_DEFAULT_TYPE === 'initials' && !empty($characterName)) {
            $words = \explode(' ', $characterName);
            $count = \count($words);
            if ($count > 1) {
                // combine the first character of each the first and the last word
                $text = \mb_strtoupper(\mb_substr($words[0], 0, 1) . \mb_substr($words[$count - 1], 0, 1));
            } else {
                // use the first two characters
                $text = \mb_strtoupper(\mb_substr($characterName, 0, 2));
            }

            $text = \htmlspecialchars($text, \ENT_XML1, 'UTF-8');

            $backgroundColor = \substr(\sha1($characterName), 0, 6);

            $perceptiveLuminance = $this->getPerceptiveLuminance(
                \hexdec($backgroundColor[0] . $backgroundColor[1]),
                \hexdec($backgroundColor[2] . $backgroundColor[3]),
                \hexdec($backgroundColor[4] . $backgroundColor[5])
            );

            $textColor = ($perceptiveLuminance < 0.5) ? '0 0 0' : '255 255 255';

            // the <path> is basically a shorter version of a <rect>
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" width="128" height="128"><path fill="#{$backgroundColor}" d="M0 0h16v16H0z"/><text x="8" y="8" fill="rgba({$textColor} / 0.89)" text-anchor="middle" dy=".3em" font-family="Arial" font-size="7">{$text}</text></svg>
SVG;

            $this->src = "data:image/svg+xml;base64," . \base64_encode($svg);
        } else {
            $this->src = WCF::getPath('rp') . 'images/avatars/avatar-default.svg';
        }
    }

    #[\Override]
    public function getHeight(): int
    {
        return $this->size;
    }

    #[\Override]
    public function getImageTag(?int $size = null): string
    {
        if ($size === null) $size = $this->size;

        return \sprintf(
            '<img src="%s" width="%d" height="%d" alt="" class="characterAvatarImage">',
            StringUtil::encodeHTML($this->getURL($size)),
            $size,
            $size
        );
    }

    /**
     * Returns the perceived luminance of the given color.
     */
    protected function getPerceptiveLuminance(int $r, int $g, int $b): float
    {
        return 1 - (0.200 * $r + 0.587 * $g + 0.114 * $b) / 255;
    }

    #[\Override]
    public function getSafeImageTag(?int $size = null): string
    {
        return \sprintf(
            '<img src="%s" width="%d" height="%d" alt="" class="characterAvatarImage">',
            StringUtil::encodeHTML($this->getSafeURL($size)),
            $size,
            $size
        );
    }

    #[\Override]
    public function getSafeURL(?int $size = null): string
    {
        return WCF::getPath('rp') . 'images/avatars/avatar-default.svg';
    }

    #[\Override]
    public function getURL(?int $size = null): string
    {
        return $this->src;
    }

    #[\Override]
    public function getWidth(): int
    {
        return $this->size;
    }
}
