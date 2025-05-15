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
 * @method  ItemDatabase    current()
 * @method  ItemDatabase[]  getObjects()
 * @method  ItemDatabase|null   getSingleObject()
 * @method  ItemDatabase|null   search($objectID)
 * @property    ItemDatabase[]  $objects
 */
class ItemDatabaseList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = ItemDatabase::class;
}
