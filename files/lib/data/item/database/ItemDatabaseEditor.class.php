<?php

namespace rp\data\item\database;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit item databases.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @mixin   ItemDatabase
 * @extends DatabaseObjectEditor<ItemDatabase>
 */
class ItemDatabaseEditor extends DatabaseObjectEditor
{
    protected static $baseClass = ItemDatabase::class;
}
