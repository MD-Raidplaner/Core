<?php

use wcf\data\option\OptionEditor;
use wcf\system\WCF;

// set default game
$game = 'default';

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
