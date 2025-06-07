<?php

namespace rp\data\character\avatar;

use rp\system\file\processor\CharacterAvatarFileProcessor;
use wcf\data\file\File;
use wcf\util\StringUtil;

/**
 * Wraps avatars to provide compatibility layers.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterAvatarDecorator implements ICharacterAvatar, ISafeFormatAvatar
{
    public function __construct(
        private readonly ICharacterAvatar | File $avatar
    ) {}

    #[\Override]
    public function getHeight(): int
    {
        if ($this->avatar instanceof File) {
            return $this->avatar->height;
        }

        return $this->avatar->getHeight();
    }

    #[\Override]
    public function getImageTag($size = null, bool $lazyLoading = true): string
    {
        if ($this->avatar instanceof File) {
            return \sprintf(
                '<img src="%s" width="%d" height="%d" alt="" class="characterAvatarImage" loading="%s">',
                StringUtil::encodeHTML($this->getSafeURL($size)),
                $size,
                $size,
                $lazyLoading ? 'lazy' : 'eager'
            );
        }

        // @phpstan-ignore arguments.count
        return $this->avatar->getImageTag($size, $lazyLoading);
    }

    #[\Override]
    public function getSafeImageTag(?int $size = null): string
    {
        if ($this->avatar instanceof File) {
            return $this->getImageTag($size);
        } elseif ($this->avatar instanceof ISafeFormatAvatar) {
            return $this->avatar->getSafeURL($size);
        }

        return $this->avatar->getURL($size);
    }

    #[\Override]
    public function getSafeURL(?int $size = null): string
    {
        if ($this->avatar instanceof File) {
            return $this->getURL($size);
        } elseif ($this->avatar instanceof ISafeFormatAvatar) {
            return $this->avatar->getSafeURL($size);
        }

        return $this->avatar->getURL($size);
    }

    #[\Override]
    public function getURL($size = null): string
    {
        if ($this->avatar instanceof File) {
            $thumbnail = $this->avatar->getThumbnail((string)CharacterAvatarFileProcessor::AVATAR_SIZE_2X)
                ?? $this->avatar->getThumbnail((string)CharacterAvatarFileProcessor::AVATAR_SIZE);
            if ($thumbnail !== null) {
                return $thumbnail->getLink();
            }

            return $this->avatar->getFullSizeImageSource();
        }

        return $this->avatar->getURL();
    }

    #[\Override]
    public function getWidth(): int
    {
        if ($this->avatar instanceof File) {
            return $this->avatar->width;
        }

        return $this->avatar->getWidth();
    }
}
