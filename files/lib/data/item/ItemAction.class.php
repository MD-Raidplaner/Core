<?php

namespace rp\data\item;

use rp\util\RPUtil;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Executes item-related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  ItemEditor[]    getObjects()
 * @method  ItemEditor  getSingleObject()
 */
class ItemAction extends AbstractDatabaseObjectAction
{
    protected $className = ItemEditor::class;

    #[\Override]
    public function create(): Item
    {
        $this->parameters['data']['time'] = TIME_NOW;

        $item = parent::create();
        $additionalData = $item->additionalData;

        $sql = "INSERT IGNORE INTO  rp1_item_index
                                    (itemName, itemID)
                VALUES              (?, ?)";
        $statement = WCF::getDB()->prepare($sql);
        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            if (isset($additionalData[$language->languageCode])) {
                $itemKey = RPUtil::generateItemUniqueKey(
                    $additionalData[$language->languageCode]['name'],
                    $additionalData['additionalData'] ?? null
                );

                $statement->execute([
                    $itemKey,
                    $item->getObjectID(),
                ]);
            }
        }

        return $item;
    }
}
