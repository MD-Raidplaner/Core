<?php

namespace rp\data\character;

use rp\data\character\avatar\CharacterAvatar;
use rp\data\character\avatar\CharacterAvatarAction;
use rp\event\character\BeforeFindCharacters;
use rp\system\character\CharacterHandler;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISearchAction;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\event\EventHandler;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\file\upload\UploadFile;
use wcf\system\request\RequestHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\ImageUtil;

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
class CharacterAction extends AbstractDatabaseObjectAction implements ISearchAction
{
    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = ['getSearchResultList'];

    /**
     * @inheritDoc
     */
    protected $className = CharacterEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.rp.canAddCharacter'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.rp.canDeleteCharacter'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.rp.canEditCharacter'];

    /**
     * @inheritDoc
     */
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

        // avatar
        if (isset($this->parameters['avatarFile']) && \is_array($this->parameters['avatarFile']) && !empty($this->parameters['avatarFile'])) {
            $avatarFile = \reset($this->parameters['avatarFile']);
            $this->uploadAvatar($avatarFile, $character);
        }

        if ($character->userID) {
            UserStorageHandler::getInstance()->reset([$character->userID], 'characterPrimaryIDs');
        }

        return $character;
    }

    /**
     * @inheritDoc
     */
    public function delete(): int
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        // delete avatars
        $avatarIDs = [];
        foreach ($this->getObjects() as $character) {
            if ($character->avatarID) {
                $avatarIDs[] = $character->avatarID;
            }
        }
        if (!empty($avatarIDs)) {
            $action = new CharacterAvatarAction($avatarIDs, 'delete');
            $action->executeAction();
        }

        return parent::delete();
    }

    /**
     * Disables characters.
     */
    public function disable(): void
    {
        foreach ($this->getObjects() as $character) {
            $character->update([
                'isDisabled' => 1
            ]);
        }

        $this->unmarkItems();
    }

    /**
     * Enables characters.
     */
    public function enable(): void
    {
        foreach ($this->getObjects() as $character) {
            $character->update([
                'isDisabled' => 0
            ]);
        }

        $this->unmarkItems();
    }

    /**
     * @inheritDoc
     */
    public function getSearchResultList(): array
    {
        $searchString = $this->parameters['data']['searchString'];
        $excludedSearchValues = $this->parameters['data']['excludedSearchValues'] ?? [];
        $list = [];

        // find characters
        $searchString = \addcslashes($searchString, '_%');

        $event = new BeforeFindCharacters($searchString);
        EventHandler::getInstance()->fire($event);

        $characterProfileList = new CharacterProfileList();
        $characterProfileList->getConditionBuilder()->add("characterName LIKE ?", [$event->getSearchString() . '%']);
        if (!empty($excludedSearchValues)) {
            $characterProfileList->getConditionBuilder()->add("characterName NOT IN (?)", [$excludedSearchValues]);
        }
        $characterProfileList->sqlLimit = 10;
        $characterProfileList->readObjects();

        foreach ($characterProfileList as $characterProfile) {
            $list[] = [
                'icon' => $characterProfile->getAvatar()->getImageTag(16),
                'label' => $characterProfile->characterName,
                'objectID' => $characterProfile->characterID,
            ];
        }

        return $list;
    }

    protected function unmarkItems(?array $characterIDs = null): void
    {
        $characterIDs ??= $this->getObjectIDs();

        if (!empty($characterIDs)) {
            ClipboardHandler::getInstance()->unmark(
                $characterIDs,
                ClipboardHandler::getInstance()->getObjectTypeID('de.md-raidplaner.rp.character')
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $this->parameters['data']['lastUpdateTime'] = TIME_NOW;

        if ($this->parameters['data']['userID'] === null) {
            $this->parameters['data']['isDisabled'] = 1;
        }

        parent::update();

        foreach ($this->getObjects() as $object) {
            if (
                isset($this->parameters['avatarFile_removedFiles'])
                && \is_array($this->parameters['avatarFile_removedFiles'])
                && !empty($this->parameters['avatarFile_removedFiles'])
                && empty($this->parameters['avatarFile'])
            ) {
                $avatarAction = new CharacterAvatarAction([$object->avatarID], 'delete');
                $avatarAction->executeAction();

                $object->update(['avatarID' => null]);
            }

            // avatar
            if (
                isset($this->parameters['avatarFile'])
                && \is_array($this->parameters['avatarFile'])
                && !empty($this->parameters['avatarFile'])
            ) {
                $avatarFile = \reset($this->parameters['avatarFile']);
                $this->uploadAvatar($avatarFile, $object->getDecoratedObject());
            }
        }
    }

    /**
     * Uploads an avatar of the character.
     */
    protected function uploadAvatar(UploadFile $avatarFile, Character $character): void
    {
        // save new image
        if (!$avatarFile->isProcessed()) {
            // rotate avatar if necessary
            $fileLocation = ImageUtil::fixOrientation($avatarFile->getLocation());

            // shrink avatar if necessary
            try {
                $fileLocation = ImageUtil::enforceDimensions(
                    $fileLocation,
                    CharacterAvatar::AVATAR_SIZE,
                    CharacterAvatar::AVATAR_SIZE,
                    false
                );
            } catch (SystemException $e) {
            }

            $extension = '';
            if (($position = \mb_strrpos($avatarFile->getFilename(), '.')) !== false) {
                $extension = \mb_strtolower(\mb_substr($avatarFile->getFilename(), $position + 1));
            }

            try {
                $returnValues = (new CharacterProfileAction([$character->characterID], 'setAvatar', [
                    'fileLocation' => $fileLocation,
                    'filename' => $avatarFile->getFilename(),
                    'extension' => $extension,
                ]))->executeAction();

                $avatar = $returnValues['returnValues']['avatar'];
                $avatarFile->setProcessed($avatar->getLocation());
            } catch (\RuntimeException $e) {
            }
        }
    }

    /**
     * Validates the disable action.
     */
    public function validateDisable()
    {
        $this->validateEnable();
    }

    /**
     * Validates the enable action.
     */
    public function validateEnable(): void
    {
        WCF::getSession()->checkPermissions(['admin.rp.canEnableCharacter']);

        if (empty($this->objects)) {
            $this->readObjects();
        }
    }

    /**
     * @inheritDoc
     */
    public function validateGetSearchResultList(): void
    {
        $this->readString('searchString', false, 'data');

        if (isset($this->parameters['data']['excludedSearchValues']) && !\is_array($this->parameters['data']['excludedSearchValues'])) {
            throw new UserInputException('excludedSearchValues');
        }
    }
}
