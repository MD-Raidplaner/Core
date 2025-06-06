<?php

namespace wcf\system\endpoint\controller\rp\events;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\event\ViewableEvent;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for the rendering of the event header title.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[GetRequest('/rp/events/{id:\d+}/content-header-title')]
final class GetEventContentHeaderTitle implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $event = ViewableEvent::getEvent($variables['id']);
        if ($event === null) {
            throw new IllegalLinkException();
        }

        $this->assertEventIsAccessible($event);
        return new JsonResponse([
            'template' => WCF::getTPL()->render('rp', 'eventContentHeaderTitle', [
                'event' => $event,
            ]),
        ]);
    }

    private function assertEventIsAccessible(ViewableEvent $event): void
    {
        if (!$event->canRead()) {
            throw new PermissionDeniedException();
        }
    }
}
