<?php

namespace rp\data\point\account;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of point account list.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @extends I18nDatabaseObjectList<PointAccount>
 */
class I18nPointAccountList extends I18nDatabaseObjectList
{
    public $i18nFields = ['title' => 'titleI18n'];
    public $className = PointAccount::class;

    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('point_account.game = ?', [\RP_CURRENT_GAME]);
    }
}
