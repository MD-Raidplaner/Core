<?php

namespace rp\system\option;

use rp\data\game\Game;
use rp\data\game\GameCache;
use wcf\data\option\Option;
use wcf\system\option\SelectOptionType;

/**
 * Option type implementation for game select lists.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class GameSelectOptionType extends SelectOptionType
{
    /**
     * @inheritDoc
     * @return  Game[]
     */
    protected function getSelectOptions(Option $option): array
    {
        $games = GameCache::getInstance()->getGames();

        \uasort($games, function (Game $a, Game $b) {
            return \strcmp($a->getTitle(), $b->getTitle());
        });

        return $games;
    }
}
