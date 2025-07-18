<?php

namespace wcf\system\endpoint\controller\rp\characters;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\character\Character;
use rp\data\character\CharacterProfile;
use wcf\http\Helper;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\WCF;

/**
 * API endpoint for rendering of the character popover.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[GetRequest('/rp/characters/{id:\d+}/popover')]
final class GetCharacterPopover implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $character = Helper::fetchObjectFromRequestParameter($variables['id'], Character::class);
        $characterProfile = new CharacterProfile($character);

        return new JsonResponse([
            'template' => $this->renderPopover($characterProfile),
        ]);
    }

    private function renderPopover(CharacterProfile $characterProfile): string
    {
        return WCF::getTPL()->render('rp', 'characterCard', [
            'character' => $characterProfile,
            'disableCharacterCardButtons' => true,
        ]);
    }
}
