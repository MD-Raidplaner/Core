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
 * API endpoint for the cancel a events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/events/{id:\d+}/cancel')]
final class CancelEvent implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $event = Helper::fetchObjectFromRequestParameter($variables['id'], Event::class);

        $this->assertEventIsCancelable($event);

        (new \rp\system\event\command\CancelEvent($event))();

        return new JsonResponse([]);
    }

    private function assertEventIsCancelable(Event $event): void
    {
        if ($event->isCanceled) {
            throw new IllegalLinkException();
        }

        if (!$event->canCancel()) {
            throw new PermissionDeniedException();
        }
    }
}
