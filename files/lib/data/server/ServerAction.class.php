<?php

namespace rp\data\server;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes server related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  ServerEditor        create()
 * @method  ServerEditor[]      getObjects()
 * @method  ServerEditor        getSingleObject()
 */
class ServerAction extends AbstractDatabaseObjectAction
{
    protected $className = ServerEditor::class;
    protected $permissionsCreate = ['admin.rp.canManageGame'];
    protected $permissionsDelete = ['admin.rp.canManageGame'];
    protected $permissionsUpdate = ['admin.rp.canManageGame'];
    protected $requireACP = ['create', 'delete', 'update'];
}
