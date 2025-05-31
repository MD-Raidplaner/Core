<?php

namespace rp\system\option;

use rp\system\game\GameHandler;
use rp\system\game\GameItem;
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
     * @return GameItem[]
     */
    #[\Override]
    protected function getSelectOptions(Option $option): array
    {
        $games = GameHandler::getInstance()->getGames();

        \uasort($games, function (GameItem $a, GameItem $b) {
            return \strcmp($a, $b);
        });

        return $games;
    }
}
