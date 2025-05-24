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
    protected $className = SkillEditor::class;
    protected $permissionsCreate = ['admin.rp.canManageGame'];
    protected $permissionsDelete = ['admin.rp.canManageGame'];
    protected $permissionsUpdate = ['admin.rp.canManageGame'];
    protected $requireACP = ['create', 'delete', 'update'];
}
