<?php

use rp\system\game\GameItem;
use wcf\data\language\item\LanguageItemEditor;
use wcf\data\option\OptionEditor;
use wcf\data\package\PackageCache;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

// set default game
$game = 'default';

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
    $game,
    'rp_current_game',
]);
OptionEditor::resetCache();


// set default point account
$sql = "INSERT INTO rp1_point_account
                    (title, description, game)
        VALUES      (?, ?, ?)";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    'Default',
    'Default-Pool',
    $game,
]);
