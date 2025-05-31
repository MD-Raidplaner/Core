<?php

namespace wcf\system\endpoint\controller\rp\characters;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\character\Character;
use rp\data\character\CharacterEditor;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * API endpoint for setting a character as primary.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/characters/{id:\d+}/setPrimary')]
final class SetPrimaryCharacter implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $character = Helper::fetchObjectFromRequestParameter($variables['id'], Character::class);

        if ($character->isPrimary) {
            throw new IllegalLinkException();
        }

        $sql = "UPDATE  rp1_member
                SET     isPrimary = 0
                WHERE   userID = ?
                    AND isPrimary = 1
                    AND game = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            $character->userID,
            $character->game,
        ]);

        $editor = new CharacterEditor($character);
        $editor->update([
            'isPrimary' => 1,
        ]);

        UserStorageHandler::getInstance()->reset([$character->userID], 'characterPrimaryIDs');

        return new JsonResponse([]);
    }
}
