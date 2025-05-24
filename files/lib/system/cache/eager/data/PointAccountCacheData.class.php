<?php

namespace rp\system\cache\eager\data;

use rp\data\point\account\PointAccount;

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class PointAccountCacheData
{
    public function __construct(
        /** @var array<int, PointAccount> */
        private readonly array $pointAccounts
    ) {}

    /**
     * Returns the point account with the given account id or `null` if no such point account exists.
     */
    public function getAccount(int $accountID): ?PointAccount
    {
        return $this->pointAccounts[$accountID] ?? null;
    }

    /**
     * Returns all point accounts.
     * 
     * @return array<int, PointAccount>
     */
    public function getAccounts(): array
    {
        return $this->pointAccounts;
    }

    /**
     * Returns the point accounts with the given account ids.
     * 
     * @param array<int> $accountIDs
     * @return array<int, PointAccount>
     */
    public function getAccountsByIDs(array $accountIDs): array
    {
        return \array_filter(
            \array_map(fn($accountID) => $this->getAccount($accountID), $accountIDs),
            fn($account) => $account !== null
        );
    }
}
