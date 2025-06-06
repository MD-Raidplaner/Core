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
use wcf\system\WCF;

/**
 * API endpoint for unpublishing an event.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/events/{id:\d+}/unpublish')]
final class UnPublishEvent implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $this->assertEventIsEditable();

        $event = Helper::fetchObjectFromRequestParameter($variables['id'], Event::class);

        (new \rp\system\event\command\EnableDisableEvent($event, true))();

        return new JsonResponse([]);
    }

    private function assertEventIsEditable(): void
    {
        WCF::getSession()->checkPermissions(['mod.rp.canEditEvent']);
    }
}
