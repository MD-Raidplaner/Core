<?php

namespace rp\data\skill;

use rp\system\cache\eager\SkillCache;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\data\language\category\LanguageCategory;
use wcf\data\language\LanguageList;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Provides functions to edit skill.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  Skill   getDecoratedObject()
 * @mixin   Skill
 */
class SkillEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    protected static $baseClass = Skill::class;

    #[\Override]
    public static function create(array $parameters = []): Skill
    {
        $titles = '';
        if (\is_array($parameters['title'])) {
            $titles = $parameters['title'];
            $parameters['title'] = '';
        }

        /** @var Skill $skill */
        $skill = parent::create($parameters);

        // save skill title
        if (!empty($titles)) {
            $skillEditor = new self($skill);
            $skillEditor->saveTitles($titles);
            $skill = new static::$baseClass($skill->skillID);
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $skill;
    }

    #[\Override]
    public static function resetCache(): void
    {
        (new SkillCache())->rebuild();
    }

    /**
     * Saves the titles of the skill in language items.
     */
    protected function saveTitles(array $titles): void
    {
        // set default value
        if (isset($titles[''])) {
            $defaultValue = $titles[''];
        } elseif (isset($titles['en'])) {
            // fallback to English
            $defaultValue = $titles['en'];
        } elseif (isset($titles[WCF::getLanguage()->getFixedLanguageCode()])) {
            // fallback to the language of the current user
            $defaultValue = $titles[WCF::getLanguage()->getFixedLanguageCode()];
        } else {
            // fallback to first title
            $defaultValue = \reset($titles);
        }

        // fetch data directly from database during framework installation
        if (!PACKAGE_ID) {
            $sql = "SELECT  *
                    FROM    wcf1_language_category
                    WHERE   languageCategory = ?";
            $statement = WCF::getDB()->prepare($sql);
            $languageCategory = $statement->fetchObject(LanguageCategory::class);

            $languages = new LanguageList();
            $languages->readObjects();
        } else {
            $languages = LanguageFactory::getInstance()->getLanguages();
            $languageCategory = LanguageFactory::getInstance()->getCategory('rp.skill');
        }

        // save new titles
        $sql = "INSERT INTO             wcf1_language_item
                                        (languageID, languageItem, languageItemValue, languageCategoryID, packageID)
                VALUES                  (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE languageItemValue = VALUES(languageItemValue),
                                        languageCategoryID = VALUES(languageCategoryID)";
        $statement = WCF::getDB()->prepare($sql);

        $title = \sprintf(
            'rp.skill.%s.%s',
            $this->getGame()->identifier,
            $this->identifier
        );

        foreach ($languages as $language) {
            $value = $titles[$language->languageCode] ?? $defaultValue;

            $statement->execute([
                $language->languageID,
                $title,
                $value,
                $languageCategory->languageCategoryID,
                $this->packageID,
            ]);
        }

        // update skill
        $this->update(['title' => $title]);
    }

    #[\Override]
    public function update(array $parameters = []): void
    {
        $titles = [];
        if (isset($parameters['title']) && \is_array($parameters['title'])) {
            if (\count($parameters['title']) > 1) {
                $titles = $parameters['title'];
                $parameters['title'] = '';
            } else {
                $parameters['title'] = \reset($parameters['title']);
            }
        }

        parent::update($parameters);

        // save skill title
        if (!empty($titles)) {
            $this->saveTitles($titles);
        }
    }
}
