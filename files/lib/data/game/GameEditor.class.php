<?php

namespace rp\data\game;

use rp\system\cache\eager\GameCache;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\data\language\category\LanguageCategory;
use wcf\data\language\LanguageList;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Provides functions to edit games.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @method      Game    getDecoratedObject()
 * @mixin       Game
 */
class GameEditor extends DatabaseObjectEditor implements IEditableCachedObject
{
    protected static $baseClass = Game::class;

    #[\Override]
    public static function create(array $parameters = []): Game
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

        $game = parent::create($parameters);

        // save game title
        if (!empty($titles)) {
            $gameEditor = new self($game);
            $gameEditor->saveTitles($titles);
            $game = new static::$baseClass($game->gameID);
        }

        self::resetCache();

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $game;
    }

    #[\Override]
    public static function deleteAll(array $objectIDs = []): int
    {
        // delete language items
        if (!empty($objectIDs)) {
            $gameList = new GameList();
            $gameList->setObjectIDs($objectIDs);
            $gameList->readObjects();

            if (\count($gameList)) {
                $sql = "DELETE FROM wcf1_language_item
                        WHERE       languageItem = ?";
                $statement = WCF::getDB()->prepare($sql);

                WCF::getDB()->beginTransaction();
                foreach ($gameList as $game) {
                    $statement->execute(['rp.game.' . $game->identifier]);
                }
                WCF::getDB()->commitTransaction();
            }
        }

        return parent::deleteAll($objectIDs);
    }

    #[\Override]
    public static function resetCache(): void
    {
        (new GameCache())->rebuild();
    }

    /**
     * Saves the titles of the game in language items.
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
            $languageCategory = LanguageFactory::getInstance()->getCategory('rp.game');
        }

        // save new titles
        $sql = "INSERT INTO             wcf1_language_item
                                        (languageID, languageItem, languageItemValue, languageCategoryID, packageID)
                VALUES                  (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE languageItemValue = VALUES(languageItemValue),
                                        languageCategoryID = VALUES(languageCategoryID)";
        $statement = WCF::getDB()->prepare($sql);

        foreach ($languages as $language) {
            $value = $titles[$language->languageCode] ?? $defaultValue;

            $statement->execute([
                $language->languageID,
                'rp.game.' . $this->identifier,
                $value,
                $languageCategory->languageCategoryID,
                $this->packageID,
            ]);
        }

        // update game
        $this->update(['title' => 'rp.game.' . $this->identifier]);
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

        // save game title
        if (!empty($titles)) {
            $this->saveTitles($titles);
        }
    }
}
