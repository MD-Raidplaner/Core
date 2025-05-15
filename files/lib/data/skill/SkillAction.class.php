<?php

namespace rp\data\skill;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes skill related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  SkillEditor create()
 * @method  SkillEditor[]   getObjects()
 * @method  SkillEditor getSingleObject()
 */
class SkillAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = SkillEditor::class;

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
