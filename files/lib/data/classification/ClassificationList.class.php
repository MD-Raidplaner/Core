<?php

namespace rp\data\classification;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of classifications.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Classification      current()
 * @method  Classification[]    getObjects()
 * @method  Classification|null     search($objectID)
 * @property    Classification[]    $objects
 */
class ClassificationList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Classification::class;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('classification.gameID = ?', [RP_CURRENT_GAME_ID]);
    }
}
