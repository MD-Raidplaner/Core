<?php

namespace rp\data\game;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\WCF;

/**
 * Game related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  GameEditor      create()
 * @method  GameEditor[]    getObjects()
 * @method  GameEditor      getSingleObject()
 */
class GameAction extends AbstractDatabaseObjectAction
{
    protected $className = GameEditor::class;
    protected $permissionsCreate = ['admin.rp.canManageGame'];
    protected $permissionsDelete = ['admin.rp.canManageGame'];
    protected $permissionsUpdate = ['admin.rp.canManageGame'];
    protected $requireACP = ['create', 'delete', 'update'];
}
