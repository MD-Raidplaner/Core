<?php

namespace rp\system\classification;

/**
 * ClassificationHandler is a singleton factory that manages the classifications in the system.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class ClassificationHandler extends SingletonFactory
{
    /**
     * @var array<string, ClassificationItem>
     */
    private array $classifications = [];

    /**
     * Returns the classification with the given fraction or `null` if no such classification exists.
     */
    public function getClassificationByFraction(string $fraction): ?ClassificationItem
    {
        return \array_find(
            $this->classifications,
            fn($classification) => \in_array(
                $fraction,
                $classification->fractions,
                true
            )
        );
    }

    /**
     * Returns the classification with the given classification identifier or `null` if no such classification exists.
     */
    public function getClassificationByIdentifier(string $identifier): ?ClassificationItem
    {
        return $this->classifications[$identifier] ?? null;
    }

    /**
     * Returns the classification with the given race or `null` if no such classification exists.
     */
    public function getClassificationByRace(string $race): ?ClassificationItem
    {
        return \array_find(
            $this->classifications,
            fn($classification) => \in_array(
                $race,
                $classification->races,
                true
            )
        );
    }

    /**
     * Returns the classification with the given role or `null` if no such classification exists.
     */
    public function getClassificationByRole(string $role): ?ClassificationItem
    {
        return \array_find(
            $this->classifications,
            fn($classification) => \in_array(
                $role,
                $classification->roles,
                true
            )
        );
    }

    /**
     * Returns the classification with the given skill or `null` if no such classification exists.
     */
    public function getClassificationBySkill(string $skill): ?ClassificationItem
    {
        return \array_find(
            $this->classifications,
            fn($classification) => \in_array(
                $skill,
                $classification->skills,
                true
            )
        );
    }

    /**
     * Returns all classifications that are currently registered.
     * 
     * @return array<string, ClassificationItem> 
     */
    public function getClassifications(): array
    {
        return $this->classifications;
    }

    /**
     * Returns all classifications that are associated with the given fractions.
     * 
     * @param array<string> $fractions
     * @return array<string, ClassificationItem>
     */
    public function getClassificationsByFractions(array $fractions): array
    {
        $classifications = [];

        foreach ($fractions as $fraction) {
            $classification = $this->getClassificationByFraction($fraction);
            if ($classification !== null) {
                $classifications[$classification->identifier] = $classification;
            }
        }

        return $classifications;
    }

    /**
     * Returns all classifications that are associated with the given races.
     * 
     * @param array<string> $races
     * @return array<string, ClassificationItem>
     */
    public function getClassificationsByRaces(array $races): array
    {
        $classifications = [];

        foreach ($races as $race) {
            $classification = $this->getClassificationByRace($race);
            if ($classification !== null) {
                $classifications[$classification->identifier] = $classification;
            }
        }

        return $classifications;
    }

    /**
     * Returns all classifications that are associated with the given roles.
     * 
     * @param array<string> $roles
     * @return array<string, ClassificationItem>
     */
    public function getClassificationsByRoles(array $roles): array
    {
        $classifications = [];

        foreach ($roles as $role) {
            $classification = $this->getClassificationByRole($role);
            if ($classification !== null) {
                $classifications[$classification->identifier] = $classification;
            }
        }

        return $classifications;
    }

    /**
     * Returns all classifications that are associated with the given skills.
     * 
     * @param array<string> $skills
     * @return array<string, ClassificationItem>
     */
    public function getClassificationsBySkills(array $skills): array
    {
        $classifications = [];

        foreach ($skills as $skill) {
            $classification = $this->getClassificationBySkill($skill);
            if ($classification !== null) {
                $classifications[$classification->identifier] = $classification;
            }
        }

        return $classifications;
    }

    #[\Override]
    protected function init(): void
    {
        $event = new ClassificationCollecting();
        EventHandler::getInstance()->fire($event);
        foreach ($event->getClassifications() as $classification) {
            if ($classification->game !== \RP_CURRENT_GAME) {
                continue; // Only load classifications for the current game
            }

            $this->classifications[$classification->identifier] = $classification;
        }
    }
}