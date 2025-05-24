<?php

namespace rp\data\character\avatar;

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
        private readonly ICharacterAvatar $avatar
    ) {}

    #[\Override]
    public function getHeight(): int
    {
        return $this->avatar->getHeight();
    }

    #[\Override]
    public function getImageTag($size = null, bool $lazyLoading = true): string
    {
        return $this->avatar->getImageTag($size, $lazyLoading);
    }

    #[\Override]
    public function getSafeImageTag(?int $size = null): string
    {
        if ($this->avatar instanceof ISafeFormatAvatar) {
            return $this->avatar->getSafeURL($size);
        }

        return $this->avatar->getURL($size);
    }

    #[\Override]
    public function getSafeURL(?int $size = null): string
    {
        if ($this->avatar instanceof ISafeFormatAvatar) {
            return $this->avatar->getSafeURL($size);
        }

        return $this->avatar->getURL($size);
    }

    #[\Override]
    public function getURL($size = null): string
    {
        return $this->avatar->getURL();
    }

    #[\Override]
    public function getWidth(): int
    {
        return $this->avatar->getWidth();
    }
}
