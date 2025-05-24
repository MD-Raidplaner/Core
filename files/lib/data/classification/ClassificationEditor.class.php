<?php

namespace rp\data\classification;

use rp\system\cache\eager\ClassificationCache;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\data\language\category\LanguageCategory;
use wcf\data\language\LanguageList;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Provides functions to edit classification.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method  Classification      getDecoratedObject()
 * @mixin   Classification
 */
class ClassificationEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    protected static $baseClass = Classification::class;

    #[\Override]
    public static function create(array $parameters = []): Classification
    {
        $titles = '';
        if (\is_array($parameters['title'])) {
            $titles = $parameters['title'];
            $parameters['title'] = '';
        }

        /** @var Classification $classification */
        $classification = parent::create($parameters);

        // save classification title
        if (!empty($titles)) {
            $classificationEditor = new self($classification);
            $classificationEditor->saveTitles($titles);
            $classification = new static::$baseClass($classification->classificationID);
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $classification;
    }

    #[\Override]
    public static function resetCache(): void
    {
        (new ClassificationCache())->rebuild();
    }

    /**
     * Saves the titles of the classification in language items.
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
            $languageCategory = LanguageFactory::getInstance()->getCategory('rp.classification');
        }

        // save new titles
        $sql = "INSERT INTO             wcf1_language_item
                                        (languageID, languageItem, languageItemValue, languageCategoryID, packageID)
                VALUES                  (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE languageItemValue = VALUES(languageItemValue),
                                        languageCategoryID = VALUES(languageCategoryID)";
        $statement = WCF::getDB()->prepare($sql);

        $title = \sprintf(
            'rp.classification.%s.%s',
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

        // update classification
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

        // save classification title
        if (!empty($titles)) {
            $this->saveTitles($titles);
        }
    }
}
