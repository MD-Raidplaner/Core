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
use wcf\system\WCF;

/**
 * API endpoint to enable a character.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/core/characters/{id:\d+}/enable')]
final class EnableCharacter implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $character = Helper::fetchObjectFromRequestParameter($variables['id'], Character::class);

        $this->assertCharacterCanBeEnabled();

        if ($character->isDisabled) {
            (new CharacterEditor($character))->update(['isDisabled' => 0]);
        }

        return new JsonResponse([]);
    }

    private function assertCharacterCanBeEnabled(): void
    {
        WCF::getSession()->checkPermissions(['admin.rp.canEditCharacter']);
    }
}
