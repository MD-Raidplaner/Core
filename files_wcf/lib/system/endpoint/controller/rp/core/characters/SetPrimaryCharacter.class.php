<?php

namespace wcf\system\endpoint\controller\rp\core\characters;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\character\Character;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * API endpoint to set a character as primary character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/core/characters/{id:\d+}/setPrimary')]
final class SetPrimaryCharacter implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $character = Helper::fetchObjectFromRequestParameter($variables['id'], Character::class);

        $this->assertCharacterCanBeSetAsPrimary($character);

        $sql = "UPDATE  rp1_character
                SET     isPrimary = CASE WHEN characterID = ? THEN 1 ELSE 0 END
                WHERE   userID = ?
                    AND game = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            $character->getObjectID(),
            $character->userID,
            $character->game
        ]);

        UserStorageHandler::getInstance()->reset([$character->userID], 'characterPrimaryIDs');

        return new JsonResponse([]);
    }

    private function assertCharacterCanBeSetAsPrimary(Character $character): void
    {
        if (!$character->canEdit()) {
            throw new PermissionDeniedException();
        }

        if ($character->isPrimary) {
            throw new \InvalidArgumentException('Character is already set as primary character.');
        }

        if ($character->isDisabled) {
            throw new \InvalidArgumentException('Cannot set a disabled character as primary character.');
        }
    }
}
