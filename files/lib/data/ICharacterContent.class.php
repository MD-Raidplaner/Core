<?php

namespace rp\data;

use wcf\data\ITitledLinkObject;

/**
 * Default interface for character related content.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
interface ICharacterContent extends ITitledLinkObject
{
    /**
     * Returns the character id.
     */
    public function getCharacterID(): int;

    /**
     * Returns the character name.
     */
    public function getCharacterName(): string;

    /**
     * Returns the timestamp of the creation.
     */
    public function getTime(): int;
}
