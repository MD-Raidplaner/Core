<?php

namespace wcf\system\endpoint\controller\rp\characters;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rp\data\character\Character;
use rp\data\character\CharacterEditor;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * API endpoint for disabling a character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/characters/{id:\d+}/disable')]
final class DisableCharacter implements IController
{
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $character = Helper::fetchObjectFromRequestParameter($variables['id'], Character::class);

        WCF::getSession()->checkPermissions(['admin.rp.canEditCharacter']);

        if ($character->isPrimary) {
            throw new IllegalLinkException();
        }

        if (!$character->isDisabled) {
            (new CharacterEditor($character))->update([
                'isDisabled' => 1,
            ]);
        }

        return new JsonResponse([]);
    }
}
