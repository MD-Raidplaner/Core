<?php

namespace rp\system\point\account\command;

use rp\data\point\account\PointAccountEditor;
use rp\event\point\account\AccountsDeleted;
use wcf\system\event\EventHandler;

/**
 * Deletes a bunch of point accounts.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International
 */
final class DeleteAccounts
{
    public function __construct(
        private readonly array $accounts,
    ) {}

    public function __invoke(): void
    {
        PointAccountEditor::deleteAll(\array_column($this->accounts, 'accountID'));

        $account = new AccountsDeleted($this->accounts);
        EventHandler::getInstance()->fire($account);
    }
}
