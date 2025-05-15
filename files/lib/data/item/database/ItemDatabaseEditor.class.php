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
 * @method static   ItemDatabase    create(array $parameters = [])
 * @method  ItemDatabase    getDecoratedObject()
 * @mixin   ItemDatabase
 */
class ItemDatabaseEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = ItemDatabase::class;
}
