<?php

namespace rp\data\game;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of games.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Game        current()
 * @method  Game[]      getObjects()
 * @method  Game|null   search($objectID)
 * @property    Game[]  $objects
 */
class GameList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Game::class;
}
