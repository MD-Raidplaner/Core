<?php

namespace rp\data\item;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of items.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @extends DatabaseObjectList<Item>
 */
class ItemList extends DatabaseObjectList
{
    public $className = Item::class;
}
