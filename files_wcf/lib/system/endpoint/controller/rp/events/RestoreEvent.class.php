<?php

namespace wcf\system\endpoint\controller\rp\events;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\event\Event;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;

/**
 * API endpoint for the restore a events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/events/{id:\d+}/restore')]
final class RestoreEvent implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $event = Helper::fetchObjectFromRequestParameter($variables['id'], Event::class);

        $this->assertEventIsEditable($event);

        (new \rp\system\event\command\RestoreEvent($event))();

        return new JsonResponse([]);
    }

    private function assertEventIsEditable(Event $event): void
    {
        if (!$event->canRestore()) {
            throw new PermissionDeniedException();
        }

        if (!$event->isDeleted) {
            throw new IllegalLinkException();
        }
    }
}
