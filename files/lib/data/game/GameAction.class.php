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
    /**
     * @inheritDoc
     */
    protected $className = GameEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canManageGame'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];
}
