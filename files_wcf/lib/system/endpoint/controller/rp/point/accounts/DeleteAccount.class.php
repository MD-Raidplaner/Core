<?php

namespace wcf\system\endpoint\controller\rp\point\accounts;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rp\data\point\account\PointAccount;
use rp\data\point\account\PointAccountEditor;
use wcf\http\Helper;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\WCF;

/**
 * API endpoint for the deletion of point accounts.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[DeleteRequest('/rp/point/accounts/{id:\d+}')]
final class DeleteAccount implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $account = Helper::fetchObjectFromRequestParameter($variables['id'], PointAccount::class);

        $this->assertAccountIsDeletable();

        $editor = new PointAccountEditor($account);
        $editor->delete();
        PointAccountEditor::resetCache();

        return new JsonResponse([]);
    }

    private function assertAccountIsDeletable(): void
    {
        WCF::getSession()->checkPermissions(['admin.rp.canManagePointAccount']);
    }
}
