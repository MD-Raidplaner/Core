<?php

namespace rp\system\cache\eager\data;

use rp\data\item\Item;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ItemCacheData
{
    public function __construct(
        /** @var array<int, Item> */
        public readonly array $items,
        /** @var array<string, int> */
        public readonly array $itemNames
    ) {}

    /**
     * Returns the item with the given item id or `null` if no such item exists.
     */
    public function getItem(int $itemID): ?Item
    {
        return $this->items[$itemID] ?? null;
    }

    /**
     * Returns the item with the given item name or `null` if no such item exists.
     */
    public function getItemByName(string $name): ?Item
    {
        $encodedName = \base64_encode($name);
        $itemID = $this->itemNames[$encodedName] ?? 0;
        return $this->getItem($itemID);
    }

    /**
     * Returns all items.
     * 
     * @return array<int, Item>
     */
    public function getItems(): array
    {
        return $this->itemNames;
    }

    /**
     * Returns the items with the given item ids.
     * 
     * @param array<int> $itemIDs
     * @return array<int, Item>
     */
    public function getItemsByIDs(array $itemIDs): array
    {
        $items = [];

        foreach ($itemIDs as $itemID) {
            if ($item = $this->getItem($itemID)) {
                $items[$item->getObjectID()] = $item;
            }
        }

        return $items;
    }
}
