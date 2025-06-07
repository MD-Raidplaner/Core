<?php

namespace rp\data\character;

use rp\system\character\CharacterHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\request\RequestHandler;
use wcf\system\user\storage\UserStorageHandler;

/**
 * Executes character related actions.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  CharacterEditor[]    getObjects()
 * @method  CharacterEditor  getSingleObject()
 */
class CharacterAction extends AbstractDatabaseObjectAction
{
    protected $className = CharacterEditor::class;
    protected $permissionsCreate = ['admin.rp.canAddCharacter'];
    protected $permissionsDelete = ['admin.rp.canDeleteCharacter'];
    protected $permissionsUpdate = ['admin.rp.canEditCharacter'];

    #[\Override]
    public function create(): Character
    {
        $this->parameters['data']['created'] = TIME_NOW;

        if ($this->parameters['data']['userID'] !== null) {
            if (RequestHandler::getInstance()->isACPRequest()) {
                $characterList = new CharacterList();
                $characterList->getConditionBuilder()->add('userID = ?', [$this->parameters['data']['userID']]);
                $characterList->getConditionBuilder()->add('isPrimary = ?', [1]);
                $this->parameters['data']['isPrimary'] = \intval($characterList->countObjects() === 0);
            } else {
                $this->parameters['data']['isPrimary'] = \intval(CharacterHandler::getInstance()->getPrimaryCharacter() === null);
            }
        } else {
            $this->parameters['data']['isPrimary'] = 1;
            $this->parameters['data']['isDisabled'] = 1;
        }

        /** @var Character $character */
        $character = parent::create();

        if ($character->userID) {
            UserStorageHandler::getInstance()->reset([$character->userID], 'characterPrimaryIDs');
        }

        return $character;
    }

    #[\Override]
    public function delete(): void
    {
        throw new \BadMethodCallException('delete() is not supported');
    }

    #[\Override]
    public function update(): void
    {
        $this->parameters['data']['lastUpdateTime'] = TIME_NOW;

        if ($this->parameters['data']['userID'] === null) {
            $this->parameters['data']['isDisabled'] = 1;
        }

        parent::update();
    }
}
