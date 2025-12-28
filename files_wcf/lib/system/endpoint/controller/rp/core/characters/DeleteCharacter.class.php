<?php

namespace wcf\system\endpoint\controller\rp\core\characters;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\character\Character;
use rp\data\character\CharacterEditor;
use rp\event\character\CharacterDeleted;
use wcf\http\Helper;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\event\EventHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\user\storage\UserStorageHandler;

/**
 * API endpoint to disable a character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[DeleteRequest('/rp/core/characters/{id:\d+}')]
final class DeleteCharacter implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $character = Helper::fetchObjectFromRequestParameter($variables['id'], Character::class);

        $this->assertCharacterCanBeDeleted($character);

        if ($character->userID) {
            UserStorageHandler::getInstance()->reset([$character->userID], 'characterPrimaryIDs');
        }

        CharacterEditor::deleteAll([$character->characterID]);

        EventHandler::getInstance()->fire(
            new CharacterDeleted([$character])
        );

        return new JsonResponse([]);
    }

    private function assertCharacterCanBeDeleted(Character $character): void
    {
        if (!$character->canDelete()) {
            throw new PermissionDeniedException();
        }
    }
}
