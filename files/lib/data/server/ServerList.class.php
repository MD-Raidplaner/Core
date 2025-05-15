<?php

namespace rp\data\server;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of servers.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Server      current()
 * @method  Server[]    getObjects()
 * @method  Server|null     search($objectID)
 * @property    Server[]    $objects
 */
class ServerList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Server::class;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('server.gameID = ?', [RP_CURRENT_GAME_ID]);
    }
}
