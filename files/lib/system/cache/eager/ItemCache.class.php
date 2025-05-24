<?php

namespace rp\system\cache\eager;

use rp\data\item\ItemList;
use rp\system\cache\eager\data\ItemCacheData;
use wcf\system\cache\eager\AbstractEagerCache;
use wcf\system\WCF;

/**
 * Eager cache implementation for items.
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractEagerCache<ItemCacheData>
 */
final class ItemCache extends AbstractEagerCache
{
    #[\Override]
    protected function getCacheData(): ItemCacheData
    {
        $itemList = new ItemList();
        $itemList->readObjects();

        $sql = "SELECT  *
                FROM    rp1_item_index";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();

        $itemNames = [];
        while ($row = $statement->fetchArray()) {
            $itemNames[\base64_encode($row['itemName'])] = $row['itemID'];
        }

        return new ItemCacheData(
            $itemList->getObjects(),
            $itemNames
        );
    }
}
