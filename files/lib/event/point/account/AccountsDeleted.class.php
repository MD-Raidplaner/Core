<?php

namespace rp\event\point\account;

use rp\data\point\account\PointAccount;
use wcf\event\IPsr14Event;

/**
 * Indicates that multiple point accounts have been deleted.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property-read PointAccount[] $accounts
 */
final class AccountsDeleted implements IPsr14Event
{
    public function __construct(
        public readonly array $accounts,
    ) {}
}
