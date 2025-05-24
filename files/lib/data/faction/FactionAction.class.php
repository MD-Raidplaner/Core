<?php

namespace rp\data\faction;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes faction related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  FactionEditor       create()
 * @method  FactionEditor[]     getObjects()
 * @method  FactionEditor       getSingleObject()
 */
class FactionAction extends AbstractDatabaseObjectAction
{
    protected $className = FactionEditor::class;
    protected $permissionsCreate = ['admin.rp.canManageGame'];
    protected $permissionsDelete = ['admin.rp.canManageGame'];
    protected $permissionsUpdate = ['admin.rp.canManageGame'];
    protected $requireACP = ['create', 'delete', 'update'];
}
