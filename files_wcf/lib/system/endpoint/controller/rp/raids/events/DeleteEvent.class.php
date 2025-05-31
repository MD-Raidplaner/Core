<?php

namespace wcf\system\endpoint\controller\rp\raids\events;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rp\data\raid\event\RaidEvent;
use rp\data\raid\event\RaidEventEditor;
use wcf\http\Helper;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\WCF;

/**
 * API endpoint for the deletion of raid events.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[DeleteRequest('/rp/raids/events/{id:\d+}')]
final class DeleteEvent implements IController
{
      #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $event = Helper::fetchObjectFromRequestParameter($variables['id'], RaidEvent::class);

        $this->assertEventIsDeletable();

        $editor = new RaidEventEditor($event);
        $editor->delete();
        RaidEventEditor::resetCache();

        return new JsonResponse([]);
    }

    private function assertEventIsDeletable(): void
    {
        WCF::getSession()->checkPermissions(['admin.rp.canManageRaidEvent']);
    }
}