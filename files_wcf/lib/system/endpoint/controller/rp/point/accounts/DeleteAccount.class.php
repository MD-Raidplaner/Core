<?php

namespace wcf\system\endpoint\controller\rp\point\accounts;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use rp\data\point\account\PointAccount;
use rp\system\point\account\command\DeleteAccounts;
use wcf\http\Helper;
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

        $this->assertAccountIsDeletable($account);

        (new DeleteAccounts([$account]))();

        return new JsonResponse([]);
    }

    private function assertAccountIsDeletable(PointAccount $account): void
    {
        WCF::getSession()->checkPermissions('admin.rp.canManagePointAccount');
    }
}
