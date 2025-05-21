<?php

namespace wcf\system\endpoint\controller\rp\characters;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rp\data\character\Character;
use rp\system\character\command\DeleteCharacters;
use wcf\http\Helper;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\PermissionDeniedException;

/**
 * API endpoint for deleting an character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[DeleteRequest('/rp/characters/{id:\d+}')]
final class DeleteCharacter implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $character = Helper::fetchObjectFromRequestParameter($variables['id'], Character::class);

        if (!$character->canDelete()) {
            throw new PermissionDeniedException();
        }

        (new DeleteCharacters([$character]))();

        return new JsonResponse([]);
    }
}
