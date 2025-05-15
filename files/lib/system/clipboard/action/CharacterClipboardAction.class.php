<?php

namespace rp\system\clipboard\action;

use rp\data\character\CharacterAction;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\clipboard\action\AbstractClipboardAction;
use wcf\system\clipboard\ClipboardEditorItem;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for character objects.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterClipboardAction extends AbstractClipboardAction
{
    /**
     * @inheritDoc
     */
    protected $actionClassActions = ['delete'];

    /**
     * @inheritDoc
     */
    protected $supportedActions = [
        'delete',
        'enable',
    ];

    /**
     * @inheritDoc
     */
    public function execute($objects, ClipboardAction $action): ?ClipboardEditorItem
    {
        $item = parent::execute($objects, $action);

        if ($item === null) {
            return null;
        }

        // handle actions
        switch ($action->actionName) {
            case 'delete':
                $item->addInternalData(
                    'confirmMessage',
                    WCF::getLanguage()->getDynamicVariable(
                        'wcf.clipboard.item.de.md-raidplaner.rp.character.delete.confirmMessage',
                        [
                            'count' => $item->getCount(),
                        ]
                    )
                );
                break;
        }

        return $item;
    }

    /**
     * @inheritDoc
     */
    public function getClassName(): string
    {
        return CharacterAction::class;
    }

    /**
     * @inheritDoc
     */
    public function getTypeName(): string
    {
        return 'de.md-raidplaner.rp.character';
    }

    /**
     * Returns the ids of the characters that can be deleted.
     *
     * @return	int[]
     */
    protected function validateDelete(): array
    {
        return \array_map(function ($character) {
            return $character->characterID;
        }, \array_filter($this->objects, function ($character) {
            return WCF::getSession()->getPermission('admin.rp.canDeleteCharacter');
        }));
    }

    /**
     * Returns the ids of the characters that can be enabled.
     *
     * @return  int[]
     */
    public function validateEnable(): array
    {
        // check permissions
        if (!WCF::getSession()->getPermission('admin.rp.canEnableCharacter')) {
            return [];
        }

        return \array_map(function ($character) {
            return $character->characterID;
        }, \array_filter($this->objects, function ($character) {
            return $character->isDisabled && $character->userID !== null;
        }));
    }
}
