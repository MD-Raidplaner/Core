<?php

namespace rp\data\point\account;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of point accounts.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @extends DatabaseObjectList<PointAccount>
 */
class PointAccountList extends DatabaseObjectList
{
    public $className = PointAccount::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('point_account.game = ?', [\RP_CURRENT_GAME]);
    }
}
