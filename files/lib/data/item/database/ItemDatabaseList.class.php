<?php

namespace rp\data\item\database;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of item databases.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @extends DatabaseObjectList<ItemDatabase>
 */
class ItemDatabaseList extends DatabaseObjectList
{
    public $className = ItemDatabase::class;
}
