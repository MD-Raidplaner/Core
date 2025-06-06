<?php

namespace wcf\system\endpoint\controller\rp\events;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\event\Event;
use rp\event\character\AvailableCharactersChecking;
use rp\system\character\CharacterHandler;
use wcf\data\object\type\ObjectTypeCache;
use wcf\http\Helper;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\event\EventHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\NamedUserException;
use wcf\system\WCF;

/**
 * API endpoint for the available characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[GetRequest('/rp/events/{id:\d+}/availableCharacters')]
final class AvailableCharacters implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $event = Helper::fetchObjectFromRequestParameter($variables['id'], Event::class);

        $this->assertEvent($event);

        // check users characters
        $event = new AvailableCharactersChecking(
            CharacterHandler::getInstance()->getCharacters(),
            $event
        );
        EventHandler::getInstance()->fire($event);

        if (!count($event->getAvailableCharacters())) {
            throw new NamedUserException(WCF::getLanguage()->get('rp.event.raid.noAvailableCharacters'));
        }

        return new JsonResponse([
            'template' => WCF::getTPL()->render(
                'rp',
                'eventAvailableCharacters',
                [
                    'availableCharacters' => $event->getAvailableCharacters(),
                ]
            ),
        ]);
    }

    private function assertEvent(Event $event): void
    {
        $objectType = ObjectTypeCache::getInstance()->getObjectTypeIDByName('de.md-raidplaner.rp.event.controller', 'de.md-raidplaner.rp.event.controller.raid');
        if (!$event->eventID || $objectType !== $event->objectTypeID) {
            throw new IllegalLinkException();
        }
    }
}
