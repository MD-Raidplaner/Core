<?php

namespace rp\data\point\account;

use rp\system\cache\builder\PointAccountCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the point account cache.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class PointAccountCache extends SingletonFactory
{
    /**
     * cached point accounts
     * @var PointAccount[]
     */
    protected array $cachedPointAccounts = [];

    /**
     * Returns the point account with the given account id or `null` if no such point account exists.
     */
    public function getAccountByID(int $accountID): ?PointAccount
    {
        return $this->cachedPointAccounts[$accountID] ?? null;
    }

    /**
     * Returns all point accounts.
     * 
     * @return  PointAccount[]
     */
    public function getAccounts(): array
    {
        return $this->cachedPointAccounts;
    }

    /**
     * Returns the point accounts with the given account ids.
     * 
     * @return	PointAccount[]
     */
    public function getAccountsByIDs(array $accountIDs): array
    {
        return \array_filter(
            \array_map(fn ($accountID) => $this->getEventByID($accountID), $accountIDs),
            fn ($account) => $account !== null
        );
    }

    /**
     * @inheritDoc
     */
    protected function init(): void
    {
        $this->cachedPointAccounts = PointAccountCacheBuilder::getInstance()->getData(['gameID' => RP_CURRENT_GAME_ID]);
    }
}
