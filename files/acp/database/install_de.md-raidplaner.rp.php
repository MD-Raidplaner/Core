<?php

/**
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */

use wcf\system\database\table\column\CharDatabaseTableColumn;
use wcf\system\database\table\column\DefaultFalseBooleanDatabaseTableColumn;
use wcf\system\database\table\column\DefaultTrueBooleanDatabaseTableColumn;
use wcf\system\database\table\column\FloatDatabaseTableColumn;
use wcf\system\database\table\column\IntDatabaseTableColumn;
use wcf\system\database\table\column\MediumtextDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar191DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\column\SmallintDatabaseTableColumn;
use wcf\system\database\table\column\TextDatabaseTableColumn;
use wcf\system\database\table\column\VarcharDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;
use wcf\system\database\table\index\DatabaseTableIndex;
use wcf\system\database\table\index\DatabaseTablePrimaryIndex;
use wcf\system\database\table\PartialDatabaseTable;

return [
    DatabaseTable::create('rp1_classification')
        ->columns([
            ObjectIdDatabaseTableColumn::create('classificationID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('icon')
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['classificationID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_classification_to_faction')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('classificationID'),
            NotNullInt10DatabaseTableColumn::create('factionID'),
        ])
        ->indices([
            DatabaseTableIndex::create('classificationID_factionID')
                ->columns(['classificationID', 'factionID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['classificationID'])
                ->referencedTable('rp1_classification')
                ->referencedColumns(['classificationID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_classification_to_race')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('classificationID'),
            NotNullInt10DatabaseTableColumn::create('raceID'),
        ])
        ->indices([
            DatabaseTableIndex::create('classificationID_raceID')
                ->columns(['classificationID', 'raceID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['classificationID'])
                ->referencedTable('rp1_classification')
                ->referencedColumns(['classificationID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_classification_to_role')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('classificationID'),
            NotNullInt10DatabaseTableColumn::create('roleID'),
        ])
        ->indices([
            DatabaseTableIndex::create('classificationID_roleID')
                ->columns(['classificationID', 'roleID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['classificationID'])
                ->referencedTable('rp1_classification')
                ->referencedColumns(['classificationID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_classification_to_skill')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('classificationID'),
            NotNullInt10DatabaseTableColumn::create('skillID'),
        ])
        ->indices([
            DatabaseTableIndex::create('classificationID_skillID')
                ->columns(['classificationID', 'skillID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['classificationID'])
                ->referencedTable('rp1_classification')
                ->referencedColumns(['classificationID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_faction')
        ->columns([
            ObjectIdDatabaseTableColumn::create('factionID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('icon')
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['factionID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_classification_to_faction')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['factionID'])
                ->referencedTable('rp1_faction')
                ->referencedColumns(['factionID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_event')
        ->columns([
            ObjectIdDatabaseTableColumn::create('eventID'),
            NotNullInt10DatabaseTableColumn::create('objectTypeID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            VarcharDatabaseTableColumn::create('title')
                ->length(191),
            IntDatabaseTableColumn::create('userID')
                ->length(10),
            NotNullVarchar255DatabaseTableColumn::create('username'),
            NotNullInt10DatabaseTableColumn::create('created'),
            NotNullInt10DatabaseTableColumn::create('startTime'),
            NotNullInt10DatabaseTableColumn::create('endTime'),
            DefaultFalseBooleanDatabaseTableColumn::create('isFullDay'),
            MediumtextDatabaseTableColumn::create('notes'),
            DefaultFalseBooleanDatabaseTableColumn::create('hasEmbeddedObjects'),
            NotNullInt10DatabaseTableColumn::create('views')
                ->defaultValue(0),
            DefaultFalseBooleanDatabaseTableColumn::create('enableComments'),
            NotNullInt10DatabaseTableColumn::create('comments')
                ->defaultValue(0),
            NotNullInt10DatabaseTableColumn::create('cumulativeLikes')
                ->defaultValue(0),
            IntDatabaseTableColumn::create('raidID')
                ->length(10),
            TextDatabaseTableColumn::create('additionalData'),
            NotNullInt10DatabaseTableColumn::create('deleteTime')
                ->defaultValue(0),
            DefaultFalseBooleanDatabaseTableColumn::create('isDeleted'),
            DefaultFalseBooleanDatabaseTableColumn::create('isCanceled'),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['eventID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['objectTypeID'])
                ->referencedTable('wcf1_object_type')
                ->referencedColumns(['objectTypeID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['userID'])
                ->referencedTable('wcf1_user')
                ->referencedColumns(['userID'])
                ->onDelete('SET NULL'),
        ]),

    DatabaseTable::create('rp1_event_raid_attendee')
        ->columns([
            ObjectIdDatabaseTableColumn::create('attendeeID'),
            NotNullInt10DatabaseTableColumn::create('eventID'),
            IntDatabaseTableColumn::create('characterID')
                ->length(10),
            NotNullVarchar255DatabaseTableColumn::create('characterName')
                ->defaultValue(''),
            NotNullVarchar255DatabaseTableColumn::create('email')
                ->defaultValue(''),
            CharDatabaseTableColumn::create('internID')
                ->length(5)
                ->notNull()
                ->defaultValue(''),
            IntDatabaseTableColumn::create('classificationID')
                ->length(10),
            IntDatabaseTableColumn::create('roleID')
                ->length(10),
            NotNullVarchar255DatabaseTableColumn::create('notes')
                ->defaultValue(''),
            NotNullInt10DatabaseTableColumn::create('created')
                ->defaultValue(0),
            DefaultFalseBooleanDatabaseTableColumn::create('addByLeader'),
            DefaultFalseBooleanDatabaseTableColumn::create('status'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['attendeeID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['eventID'])
                ->referencedTable('rp1_event')
                ->referencedColumns(['eventID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['classificationID'])
                ->referencedTable('rp1_classification')
                ->referencedColumns(['classificationID'])
                ->onDelete('SET NULL'),
        ]),

    DatabaseTable::create('rp1_game')
        ->columns([
            ObjectIdDatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['gameID']),
            DatabaseTableIndex::create('identifier')
                ->columns(['identifier'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_classification')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_event')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_faction')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_item')
        ->columns([
            ObjectIdDatabaseTableColumn::create('itemID'),
            NotNullVarchar255DatabaseTableColumn::create('searchItemID'),
            NotNullInt10DatabaseTableColumn::create('time')
                ->defaultValue(0),
            TextDatabaseTableColumn::create('additionalData'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['itemID']),
        ]),

    DatabaseTable::create('rp1_item_index')
        ->columns([
            VarcharDatabaseTableColumn::create('itemName')
            ->length(64)
            ->notNull(),
            NotNullInt10DatabaseTableColumn::create('itemID'),
        ])
        ->indices([
            DatabaseTableIndex::create('itemName_itemID')
                ->columns(['itemName', 'itemID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['itemID'])
                ->referencedTable('rp1_item')
                ->referencedColumns(['itemID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_item_database')
        ->columns([
            NotNullVarchar191DatabaseTableColumn::create('identifier'),
            IntDatabaseTableColumn::create('packageID')
                ->length(10),
            NotNullVarchar255DatabaseTableColumn::create('className'),
        ])
        ->indices([
            DatabaseTableIndex::create('identifier')
                ->columns(['identifier'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_item_to_raid')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('itemID'),
            NotNullInt10DatabaseTableColumn::create('characterID'),
            NotNullInt10DatabaseTableColumn::create('raidID'),
            IntDatabaseTableColumn::create('pointAccountID')
                ->length(10),
            FloatDatabaseTableColumn::create('points')
                ->length(11)
                ->decimals(2)
                ->notNull()
                ->defaultValue(0),
        ])
        ->indices([
            DatabaseTableIndex::create('itemID_characterID_raidID') //
                ->columns(['itemID', 'characterID', 'raidID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['itemID'])
                ->referencedTable('rp1_item')
                ->referencedColumns(['itemID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_member')
        ->columns([
            ObjectIdDatabaseTableColumn::create('characterID'),
            VarcharDatabaseTableColumn::create('characterName')
                ->length(100)
                ->notNull(),
            IntDatabaseTableColumn::create('userID')
                ->length(10),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            IntDatabaseTableColumn::create('avatarID')
                ->length(10),
            NotNullInt10DatabaseTableColumn::create('created')
                ->defaultValue(0),
            NotNullInt10DatabaseTableColumn::create('lastUpdateTime')
                ->defaultValue(0),
            MediumtextDatabaseTableColumn::create('notes'),
            TextDatabaseTableColumn::create('additionalData'),
            VarcharDatabaseTableColumn::create('guildName')
                ->length(100)
                ->notNull()
                ->defaultValue(''),
            NotNullInt10DatabaseTableColumn::create('views')
                ->defaultValue(0),
            DefaultFalseBooleanDatabaseTableColumn::create('isPrimary'),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['characterID']),
            DatabaseTableIndex::create('characterName_gameID')
                ->columns(['characterName', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['userID'])
                ->referencedTable('wcf1_user')
                ->referencedColumns(['userID'])
                ->onDelete('SET NULL'),
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_event_raid_attendee')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['characterID'])
                ->referencedTable('rp1_member')
                ->referencedColumns(['characterID'])
                ->onDelete('SET NULL'),
        ]),

    PartialDatabaseTable::create('rp1_item_to_raid')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['characterID'])
                ->referencedTable('rp1_member')
                ->referencedColumns(['characterID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_member_avatar')
        ->columns([
            ObjectIdDatabaseTableColumn::create('avatarID'),
            NotNullVarchar255DatabaseTableColumn::create('avatarName')
                ->defaultValue(''),
            VarcharDatabaseTableColumn::create('avatarExtension')
                ->length(7)
                ->notNull()
                ->defaultValue(''),
            SmallintDatabaseTableColumn::create('width')
                ->length(5)
                ->notNull()
                ->defaultValue(0),
            SmallintDatabaseTableColumn::create('height')
                ->length(5)
                ->notNull()
                ->defaultValue(0),
            IntDatabaseTableColumn::create('characterID')
                ->length(10),
            VarcharDatabaseTableColumn::create('fileHash')
                ->length(40)
                ->notNull()
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('hasWebP'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['avatarID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['characterID'])
                ->referencedTable('rp1_member')
                ->referencedColumns(['characterID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_member')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['avatarID'])
                ->referencedTable('rp1_member_avatar')
                ->referencedColumns(['avatarID'])
                ->onDelete('SET NULL'),
        ]),

    DatabaseTable::create('rp1_point_account')
        ->columns([
            ObjectIdDatabaseTableColumn::create('accountID'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('description')
                ->defaultValue(''),
            NotNullInt10DatabaseTableColumn::create('gameID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['accountID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_item_to_raid')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['pointAccountID'])
                ->referencedTable('rp1_point_account')
                ->referencedColumns(['accountID'])
                ->onDelete('SET NULL'),
        ]),

    DatabaseTable::create('rp1_race')
        ->columns([
            ObjectIdDatabaseTableColumn::create('raceID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('icon')
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['raceID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_classification_to_race')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['raceID'])
                ->referencedTable('rp1_race')
                ->referencedColumns(['raceID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_race_to_faction')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('raceID'),
            NotNullInt10DatabaseTableColumn::create('factionID'),
        ])
        ->indices([
            DatabaseTableIndex::create('raceID_factionID')
                ->columns(['raceID', 'factionID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['raceID'])
                ->referencedTable('rp1_race')
                ->referencedColumns(['raceID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['factionID'])
                ->referencedTable('rp1_faction')
                ->referencedColumns(['factionID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_raid')
        ->columns([
            ObjectIdDatabaseTableColumn::create('raidID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullInt10DatabaseTableColumn::create('raidEventID'),
            NotNullInt10DatabaseTableColumn::create('time'),
            NotNullVarchar255DatabaseTableColumn::create('addedBy'),
            NotNullVarchar255DatabaseTableColumn::create('updatedBy')
                ->defaultValue(''),
            FloatDatabaseTableColumn::create('points')
                ->length(11)
                ->decimals(2)
                ->notNull()
                ->defaultValue(0),
            MediumtextDatabaseTableColumn::create('notes'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['raidID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_item_to_raid')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['raidID'])
                ->referencedTable('rp1_raid')
                ->referencedColumns(['raidID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_raid_attendee')
        ->columns([
            NotNullInt10DatabaseTableColumn::create('raidID'),
            IntDatabaseTableColumn::create('characterID')
                ->length(10),
            NotNullVarchar255DatabaseTableColumn::create('characterName'),
            IntDatabaseTableColumn::create('classificationID')
                ->length(10),
            IntDatabaseTableColumn::create('roleID')
                ->length(10),
        ])
        ->indices([
            DatabaseTableIndex::create('raidID_characterID')
                ->columns(['raidID', 'characterID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['classificationID'])
                ->referencedTable('rp1_classification')
                ->referencedColumns(['classificationID'])
                ->onDelete('SET NULL'),
            DatabaseTableForeignKey::create()
                ->columns(['characterID'])
                ->referencedTable('rp1_member')
                ->referencedColumns(['characterID'])
                ->onDelete('SET NULL'),
            DatabaseTableForeignKey::create()
                ->columns(['raidID'])
                ->referencedTable('rp1_raid')
                ->referencedColumns(['raidID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['roleID'])
                ->referencedTable('rp1_role')
                ->referencedColumns(['roleID'])
                ->onDelete('SET NULL'),
        ]),

    DatabaseTable::create('rp1_raid_event')
        ->columns([
            ObjectIdDatabaseTableColumn::create('eventID'),
            NotNullVarchar255DatabaseTableColumn::create('title')
                ->defaultValue(''),
            IntDatabaseTableColumn::create('pointAccountID')
                ->length(10),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            FloatDatabaseTableColumn::create('defaultPoints')
                ->length(11)
                ->decimals(2)
                ->notNull()
                ->defaultValue(0),
            NotNullVarchar255DatabaseTableColumn::create('icon')
                ->defaultValue(''),
            DefaultTrueBooleanDatabaseTableColumn::create('showProfile'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['eventID']),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['pointAccountID'])
                ->referencedTable('rp1_point_account')
                ->referencedColumns(['accountID'])
                ->onDelete('SET NULL'),
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_raid')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['raidEventID'])
                ->referencedTable('rp1_raid_event')
                ->referencedColumns(['eventID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_role')
        ->columns([
            ObjectIdDatabaseTableColumn::create('roleID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('icon')
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['roleID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_classification_to_role')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['roleID'])
                ->referencedTable('rp1_role')
                ->referencedColumns(['roleID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_event_raid_attendee')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['roleID'])
                ->referencedTable('rp1_role')
                ->referencedColumns(['roleID'])
                ->onDelete('SET NULL'),
        ]),

    DatabaseTable::create('rp1_server')
        ->columns([
            ObjectIdDatabaseTableColumn::create('serverID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('type')
                ->defaultValue(''),
            NotNullVarchar255DatabaseTableColumn::create('serverGroup')
                ->defaultValue(''),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['serverID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_skill')
        ->columns([
            ObjectIdDatabaseTableColumn::create('skillID'),
            NotNullInt10DatabaseTableColumn::create('gameID'),
            NotNullVarchar255DatabaseTableColumn::create('identifier'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('icon')
                ->defaultValue(''),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
            NotNullInt10DatabaseTableColumn::create('packageID'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['skillID']),
            DatabaseTableIndex::create('identifier_gameID')
                ->columns(['identifier', 'gameID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['gameID'])
                ->referencedTable('rp1_game')
                ->referencedColumns(['gameID'])
                ->onDelete('CASCADE'),
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),

    PartialDatabaseTable::create('rp1_classification_to_skill')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['skillID'])
                ->referencedTable('rp1_skill')
                ->referencedColumns(['skillID'])
                ->onDelete('CASCADE'),
        ]),
];
