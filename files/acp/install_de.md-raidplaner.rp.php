<?php

use wcf\data\language\item\LanguageItemEditor;
use wcf\data\option\OptionEditor;
use wcf\data\package\PackageCache;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

// set default game
$sql = "INSERT INTO rp1_game
                    (identifier, title, packageID)
        VALUES      (?, ?, ?)";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    'default',
    'rp.game.default',
    PackageCache::getInstance()->getPackageByIdentifier('de.md-raidplaner.rp')->packageID,
]);
$gameID = WCF::getDB()->getInsertID('rp1_game', 'gameID');

$titles = [
    'de' => 'Standard',
    'en' => 'Default',
];

foreach ($titles as $languageCode => $value) {
    $language = LanguageFactory::getInstance()->getLanguageByCode($languageCode);

    $sql = "SELECT  languageCategoryID
            FROM    wcf1_language_category
            WHERE   languageCategory = ?";
    $statement = WCF::getDB()->prepare($sql, 1);
    $statement->execute(['rp.game']);
    $languageCategoryID = $statement->fetchSingleColumn();

    LanguageItemEditor::create([
        'languageID' => $language->languageID,
        'languageItem' => 'rp.game.default',
        'languageItemValue' => $value,
        'languageCategoryID' => $languageCategoryID,
        'packageID' => PackageCache::getInstance()->getPackageByIdentifier('de.md-raidplaner.rp')->packageID,
    ]);
}

// set default game to option
$sql = "UPDATE  wcf1_option
        SET     optionValue = ?
        WHERE   optionName = ?";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    $gameID,
    'rp_current_game_id',
]);
OptionEditor::resetCache();


// set default point account
$sql = "INSERT INTO rp1_point_account
                    (title, description, gameID)
        VALUES      (?, ?, ?)";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    'Default',
    'Default-Pool',
    $gameID,
]);
