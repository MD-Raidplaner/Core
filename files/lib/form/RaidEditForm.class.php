<?php

namespace rp\form;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\raid\Raid;
use rp\system\cache\eager\ItemCache;
use rp\system\cache\eager\PointAccountCache;
use rp\system\cache\runtime\CharacterRuntimeCache;
use rp\system\form\builder\field\item\ItemFormField;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the raid edit form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class RaidEditForm extends RaidAddForm
{
    public $formAction = 'edit';

    #[\Override]
    public function readParameters(): void
    {
        parent::readParameters();

        try {
            $parameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        id: positive-int
                    }
                    EOT
            );
            $this->formObject = new Raid($parameters['id']);

            if (!$this->formObject->getObjectID()) {
                throw new IllegalLinkException();
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }

    #[\Override]
    protected function finalizeForm(): void
    {
        /** @var ItemFormField $itemFormField */
        $itemFormField = $this->form->getNodeById('items');
        if ($itemFormField) {
            $sql = "SELECT  *
                    FROM    rp1_item_to_raid
                    WHERE   raidID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->formObject->getObjectID()]);

            $items = [];
            while ($row = $statement->fetchArray()) {
                $character = CharacterRuntimeCache::getInstance()->getObject($row['characterID']);
                $item = (new ItemCache())->getCache()->getItem($row['itemID']);
                $pointAccount = (new PointAccountCache())->getCache()->getAccount($row['pointAccountID']);

                $items[] = [
                    'characterID' => $character->getObjectID(),
                    'characterName' => $character->getTitle(),
                    'itemID' => $item->getObjectID(),
                    'itemName' => $item->getTitle(),
                    'pointAccountID' => $pointAccount->getObjectID(),
                    'pointAccountName' => $pointAccount->getTitle(),
                    'points' => $row['points']
                ];
            }

            $itemFormField->value($items);
        }
    }
}
