<?php

namespace rp\event\character;

use wcf\event\IPsr14Event;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class BeforeFindCharacters implements IPsr14Event
{
    public function __construct(
        private readonly string $searchString
    ) {
    }

    public function getSearchString(): string
    {
        return $this->searchString;
    }

    public function setSearchString(string $searchString): void
    {
        $this->searchString = $searchString;
    }
}
