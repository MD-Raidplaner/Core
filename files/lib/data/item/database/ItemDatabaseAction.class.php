<?php

namespace rp\data\item\database;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes item database-related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  ItemDatabase    create()
 * @method  ItemDatabaseEditor[]    getObjects()
 * @method  ItemDatabaseEditor  getSingleObject()
 */
class ItemDatabaseAction extends AbstractDatabaseObjectAction
{
    protected $className = ItemDatabaseEditor::class;
}
