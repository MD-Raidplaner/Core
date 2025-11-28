<?php

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */

use wcf\system\database\table\column\DefaultFalseBooleanDatabaseTableColumn;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\MediumtextDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\TextDatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;

return [
    DatabaseTable::create('rp1_character')
        ->columns([
            ObjectIdDatabaseTableColumn::create('characterID'),
            VarcharDatabaseTableColumn::create('characterName')
                ->length(100)
                ->notNull(),
            IntDatabaseTableColumn::create('userID')
                ->length(10),
            VarcharDatabaseTableColumn::create('game')
                ->length(100)
                ->notNull(),
            IntDatabaseTableColumn::create('avatarFileID')
                ->length(10),
            NotNullInt10DatabaseTableColumn::create('created')
                ->defaultValue(0),
            NotNullInt10DatabaseTableColumn::create('lastUpdateTime')
                ->defaultValue(0),
            VarcharDatabaseTableColumn::create('guildName')
                ->length(100)
                ->notNull()
                ->defaultValue(''),
            NotNullInt10DatabaseTableColumn::create('views')
                ->defaultValue(0),
            DefaultFalseBooleanDatabaseTableColumn::create('isPrimary'),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
            TextDatabaseTableColumn::create('notes'),
            MediumtextDatabaseTableColumn::create('additionalData'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['characterID']),
            DatabaseTableIndex::create('characterName_game')
                ->columns(['characterName', 'game'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['userID'])
                ->referencedTable('wcf1_user')
                ->referencedColumns(['userID'])
                ->onDelete('SET NULL'),
            DatabaseTableForeignKey::create()
                ->columns(['avatarFileID'])
                ->referencedTable('wcf1_file')
                ->referencedColumns(['fileID'])
                ->onDelete('SET NULL'),
        ]),
];
