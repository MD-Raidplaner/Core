<?php

namespace rp\system\item;

use rp\data\item\database\ItemDatabase;
use rp\data\item\database\ItemDatabaseList;
use rp\data\item\Item;
use rp\data\item\ItemAction;
use rp\system\cache\eager\ItemCache;
use rp\util\RPUtil;
use wcf\data\user\User;
use wcf\system\language\LanguageFactory;
use wcf\system\session\SessionHandler;
use wcf\system\SingletonFactory;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Handles items.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ItemHandler extends SingletonFactory
{
    /**
     * item database list
     */
    protected ?ItemDatabaseList $databases = null;

    /**
     * Returns an item based on the item name
     */
    final public function getSearchItem(string $itemName = '', int $itemID = 0, bool $refresh = false, string $additionalData = ''): ?Item
    {
        $itemName = StringUtil::trim($itemName);
        if (empty($itemName) && $itemID === 0) {
            return null;
        }

        $item = $searchItemID = null;
        if ($itemID) {
            $item = (new ItemCache())->getCache()->getItem($itemID);
            $additionalData = $item ? $item->additionalData['additionalData'] : "";
        } else {
            $itemKey = RPUtil::generateItemUniqueKey($itemName, $additionalData);
            $item = (new ItemCache())->getCache()->getItemByName($itemKey);
        }

        $searchItemID = $item ? $item->searchItemID : null;
        if ($item === null || $refresh) {
            $newItem = null;
            $user = WCF::getUser();

            try {
                SessionHandler::getInstance()->changeUser(new User(null), true);
                if (!WCF::debugModeIsEnabled()) {
                    \ob_start();
                }

                if ($this->databases !== null) {
                    /** @var ItemDatabase $database */
                    foreach ($this->databases as $database) {
                        $parser = new $database->className();

                        // read only item id
                        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
                            $searchItemID ??= $parser->searchItemID($itemName, $language);
                            if ($searchItemID > 0) {
                                break;
                            }
                        }

                        // read item multi language
                        $newItem = [];
                        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
                            $itemData = $parser->getItemData(
                                $searchItemID,
                                $language,
                                $additionalData
                            );

                            if (!empty($itemData)) {
                                $newItem[$language->languageCode] = $itemData;
                            }
                        }

                        if (!empty($newItem)) {
                            $newItem['id'] = $searchItemID;
                            $newItem['additionalData'] = $additionalData;
                        } else {
                            $newItem = null;
                        }

                        if ($newItem !== null) break;
                    }
                }
            } catch (\Exception $e) {
                // Handle exception if necessary
            } finally {
                if (!WCF::debugModeIsEnabled()) {
                    \ob_end_clean();
                }
                SessionHandler::getInstance()->changeUser($user, true);
            }

            $saveSearchItemID = '';
            if ($newItem !== null) {
                $saveSearchItemID = $newItem['id'];
                unset($newItem['id']);
            } else {
                $newItem = [];
            }

            if ($item) {
                $action = new ItemAction(
                    [$item],
                    'update',
                    [
                        'data' => [
                            'additionalData' => \serialize($newItem),
                            'searchItemID' => $saveSearchItemID,
                        ]
                    ]
                );
                $action->executeAction();

                // reload item
                $item = new Item($item->itemID);
            } else if (!empty($newItem)) {
                $action = new ItemAction(
                    [],
                    'create',
                    [
                        'data' => [
                            'additionalData' => \serialize($newItem),
                            'searchItemID' => $saveSearchItemID,
                        ]
                    ]
                );
                $item = $action->executeAction()['returnValues'];
            }
        }

        return $item;
    }

    #[\Override]
    protected function init(): void
    {
        if (!empty(RP_ITEM_DATABASES)) {
            $list = new ItemDatabaseList();
            $list->getConditionBuilder()->add('identifier IN (?)', [\explode(',', RP_ITEM_DATABASES)]);
            $list->readObjects();
            $this->databases = $list;
        }
    }
}
