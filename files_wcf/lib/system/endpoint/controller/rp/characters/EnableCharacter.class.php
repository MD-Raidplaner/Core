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
use wcf\system\WCF;

/**
 * API endpoint for enabling an character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/characters/{id:\d+}/enable')]
final class EnableCharacter implements IController
{
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $character = Helper::fetchObjectFromRequestParameter($variables['id'], Character::class);

        WCF::getSession()->checkPermissions(['admin.rp.canEditCharacter']);

        if ($character->isDisabled) {
            (new CharacterEditor($character))->update([
                'isDisabled' => 0,
            ]);
        }

        return new JsonResponse([]);
    }
}
