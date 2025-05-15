<?php

namespace rp\system\cache\builder;

use rp\data\item\ItemList;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches all items.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ItemCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    public function rebuild(array $parameters): array
    {
        $data = [
            'itemNames' => [],
            'items' => [],
        ];

        $itemList = new ItemList();
        $itemList->readObjects();
        $data['items'] = $itemList->getObjects();

        $sql = "SELECT  *
                FROM    rp1_item_index";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();
        while ($row = $statement->fetchArray()) {
            $data['itemNames'][\base64_encode($row['itemName'])] = $row['itemID'];
        }

        return $data;
    }
}
