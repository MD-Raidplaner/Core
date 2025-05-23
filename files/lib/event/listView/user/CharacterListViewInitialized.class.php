<?php

namespace rp\event\listView\user;

use rp\system\listView\user\CharacterListView;
use wcf\event\IPsr14Event;

/**
 * Indicates that the character list view has been initialized.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class CharacterListViewInitialized implements IPsr14Event
{
    public function __construct(public readonly CharacterListView $listView) {}
}
