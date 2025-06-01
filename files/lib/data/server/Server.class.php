<?php

namespace rp\data\server;

use rp\data\game\Game;
use rp\system\game\GameHandler;
use wcf\data\DatabaseObject;
use wcf\data\ITitledObject;
use wcf\system\WCF;

/**
 * Represents a server.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read   int $serverID       unique id of the server
 * @property-read   int $gameID     id of the game
 * @property-read   string  $identifier     unique textual identifier of the server identifier
 * @property-read   string  $title      title of the server or name of language item which contains the title
 * @property-read   string  $type       type of the server
 * @property-read   int $serverGroup        group of the server
 * @property-read   int $packageID      id of the package which delivers the server
 */
final class Server extends DatabaseObject implements ITitledObject
{
    /**
     * Returns game object.
     */
    public function getGame(): ?Game
    {
        return GameHandler::getInstance()->getGameByIdentifier($this->game);
    }

    /**
     * Returns language group name.
     */
    public function getGroupName(): string
    {
        return WCF::getLanguage()->get('rp.server.' . $this->getGame()->identifier . '.group.' . $this->serverGroup);
    }

    /**
     * Returns the image folder of the game.
     */
    public function getImagePath(): string
    {
        return WCF::getPath('rp') . 'images/' . $this->getGame()->identifier . '/';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return WCF::getLanguage()->get($this->title);
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getTitle();
    }
}
