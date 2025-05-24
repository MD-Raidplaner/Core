<?php

namespace rp\data\classification;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes classification related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  ClassificationEditor        create()
 * @method  ClassificationEditor[]      getObjects()
 * @method  ClassificationEditor        getSingleObject()
 */
class ClassificationAction extends AbstractDatabaseObjectAction
{
    protected $className = ClassificationEditor::class;
    protected $permissionsCreate = ['admin.rp.canManageGame'];
    protected $permissionsDelete = ['admin.rp.canManageGame'];
    protected $permissionsUpdate = ['admin.rp.canManageGame'];
    protected $requireACP = ['create', 'delete', 'update'];
}
