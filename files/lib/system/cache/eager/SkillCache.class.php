<?php

namespace rp\system\cache\eager;

use rp\data\skill\SkillList;
use rp\system\cache\eager\data\SkillCacheData;
use wcf\system\cache\eager\AbstractEagerCache;

/**
 * Eager cache implementation for skills.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractEagerCache<SkillCacheData>
 */
final class SkillCache extends AbstractEagerCache
{
    public function __construct(
        private readonly int $gameID = \RP_CURRENT_GAME_ID
    ) {}

    #[\Override]
    protected function getCacheData(): SkillCacheData
    {
        $skillList = new SkillList();
        $skillList->getConditionBuilder()->add('gameID = ?', [$this->gameID]);
        $skillList->getConditionBuilder()->add('isDisabled = ?', [0]);
        $skillList->readObjects();

        $identifiers = [];
        foreach ($skillList as $skill) {
            $identifiers[$skill->identifier] = $skill->getObjectID();
        }

        return new SkillCacheData(
            $skillList->getObjects(),
            $identifiers
        );
    }
}
