<?php

namespace rp\data\skill;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of skills.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @method  Skill   current()
 * @method  Skill[] getObjects()
 * @method  Skill|null  search($objectID)
 * @property    Skill[] $objects
 */
class SkillList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Skill::class;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->conditionBuilder->add('skill.gameID = ?', [RP_CURRENT_GAME_ID]);
    }
}
