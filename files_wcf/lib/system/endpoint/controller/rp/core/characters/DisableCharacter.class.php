<?php

namespace wcf\system\endpoint\controller\rp\core\characters;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\character\Character;
use rp\data\character\CharacterEditor;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint to disable a character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/core/characters/{id:\d+}/disable')]
final class DisableCharacter implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $character = Helper::fetchObjectFromRequestParameter($variables['id'], Character::class);

        $this->assertCharacterCanBeDisabled($character);

        if (!$character->isDisabled) {
            (new CharacterEditor($character))->update(['isDisabled' => 1]);
        }

        return new JsonResponse([]);
    }

    private function assertCharacterCanBeDisabled(Character $character): void
    {
        WCF::getSession()->checkPermissions(['admin.rp.canEditCharacter']);

        if ($character->isPrimary) {
            throw new PermissionDeniedException('Cannot disable primary character.');
        }
    }
}
