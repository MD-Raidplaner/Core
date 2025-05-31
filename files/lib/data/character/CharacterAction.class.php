<?php

namespace rp\data\character;

use rp\data\character\avatar\CharacterAvatar;
use rp\data\character\avatar\CharacterAvatarAction;
use rp\event\character\BeforeFindCharacters;
use rp\system\character\CharacterHandler;
use wcf\data\AbstractDatabaseObjectAction;
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
}
