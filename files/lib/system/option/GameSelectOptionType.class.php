<?php

namespace rp\system\option;

use rp\system\game\Game;
use rp\system\game\GameEngine;
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
     * @return array<string, Game>
     */
    #[\Override]
    protected function getSelectOptions(Option $option): array
    {
        $games = GameEngine::getInstance()->games;
        \uasort($games, fn(Game $a, Game $b) => \strcmp($a->getTitle(), $b->getTitle()));
        return $games;
    }
}
