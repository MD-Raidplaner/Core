<?php

namespace rp\data\role;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of roles.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Role        current()
 * @method  Role[]      getObjects()
 * @method  Role|null   search($objectID)
 * @property    Role[]  $objects
 */
class RoleList extends DatabaseObjectList
{
    public $className = Role::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('role.gameID = ?', [RP_CURRENT_GAME_ID]);
    }
}
