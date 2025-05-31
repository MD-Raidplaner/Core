<?php

namespace rp\system;

use rp\page\CalendarPage;
use rp\system\character\point\CharacterPointHandler;
use rp\system\game\GameHandler;
use rp\system\game\GameItem;
use wcf\system\application\AbstractApplication;

// define current raidplaner version
\define('RP_VERSION', '1.0.0 dev 1');

/**
 * This class extends the main WCF class by raidplaner specific functions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RPCore extends AbstractApplication
{
    protected $abbreviation = 'rp';
    protected static GameItem $gameObj;
    protected $primaryController = CalendarPage::class;

    #[\Override]
    public function __run()
    {
        $this->initGame();
    }

    /**
     * Returns the character point handler
     */
    public function getCharacterPointHandler(): CharacterPointHandler
    {
        return CharacterPointHandler::getInstance();
    }

    /**
     * Returns the current game object.
     */
    public function getGame(): Game
    {
        return self::$gameObj;
    }

    /**
     * Initialises the current game.
     */
    protected function initGame(): void
    {
        self::$gameObj = GameHandler::getInstance()->getCurrentGame();
    }
}
