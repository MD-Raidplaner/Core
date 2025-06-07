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
 * @extends DatabaseObjectList<Server>
 */
class ServerList extends DatabaseObjectList
{
    public $className = Server::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('server.game = ?', [RP_CURRENT_GAME]);
    }
}
