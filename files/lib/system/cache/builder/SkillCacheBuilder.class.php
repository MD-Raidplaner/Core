<?php

namespace rp\system\cache\builder;

use rp\data\skill\Skill;
use wcf\system\cache\builder\AbstractCacheBuilder;
use wcf\system\WCF;

/**
 * Caches the skill.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class SkillCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters): array
    {
        $data = [
            'skill' => [],
            'identifier' => [],
        ];

        // get game skill
        $sql = "SELECT  *
                FROM    rp1_skill
                WHERE   isDisabled = ?
                    AND gameID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([
            0,
            $parameters['gameID'],
        ]);

        /** @var Skill $object */
        while ($object = $statement->fetchObject(Skill::class)) {
            $data['skill'][$object->getObjectID()] = $object;
            $data['identifier'][$object->identifier] = $object->getObjectID();
        }

        return $data;
    }
}
