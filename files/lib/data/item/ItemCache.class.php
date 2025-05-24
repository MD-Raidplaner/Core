<?php

namespace rp\data\item;

use rp\system\cache\builder\ItemCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the item cache.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ItemCache extends SingletonFactory
{
    /**
     * cached item by item name
     * @var int[]
     */
    protected array $cachedItemNames = [];

    /**
     * cached items
     * @var Item[]
     */
    protected array $cachedItems = [];

    /**
     * Returns the item with the given item id or `null` if no such item exists.
     */
    public function getItemByID(int $itemID): ?Item
    {
        return $this->cachedItems[$itemID] ?? null;
    }

    /**
     * Returns the item with the given item name or `null` if no such item exists.
     */
    public function getItemByName(string $name): ?Item
    {
        $encodedName = \base64_encode($name);
        $itemID = $this->cachedItemNames[$encodedName] ?? 0;
        return $this->getItemByID($itemID);
    }

    /**
     * Returns all items.
     * 
     * @return  Item[]
     */
    public function getItems(): array
    {
        return $this->cachedItems;
    }

    /**
     * Returns the items with the given item ids.
     * 
     * @return	Item[]
     */
    public function getItemsByIDs(array $itemIDs): array
    {
        $items = [];

        foreach ($itemIDs as $itemID) {
            if ($item = $this->getItemByID($itemID)) {
                $items[$item->getObjectID()] = $item;
            }
        }

        return $items;
    }

    #[\Override]
    protected function init(): void
    {
        $cacheBuilder = ItemCacheBuilder::getInstance();
        $this->cachedItemNames = $cacheBuilder->getData([], 'itemNames');
        $this->cachedItems = $cacheBuilder->getData([], 'items');
    }
}
