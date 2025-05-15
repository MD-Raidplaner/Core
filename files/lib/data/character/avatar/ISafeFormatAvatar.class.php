<?php

namespace rp\data\character\avatar;

/**
 * A safe avatar supports a broadly supported fallback image format.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
interface ISafeFormatAvatar extends ICharacterAvatar
{
    /**
     * @see ICharacterAvatar::getURL()
     */
    public function getSafeURL(?int $size = null): string;

    /**
     * @see ICharacterAvatar::getImageTag()
     */
    public function getSafeImageTag(?int $size = null): string;
}
