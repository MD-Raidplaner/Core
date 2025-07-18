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
    DatabaseTable::create('rp1_event')
        ->columns([
            ObjectIdDatabaseTableColumn::create('eventID'),
            NotNullVarchar255DatabaseTableColumn::create('eventType'),
            NotNullVarchar255DatabaseTableColumn::create('game'),
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
            VarcharDatabaseTableColumn::create('classification')
                ->length(255),
            NotNullVarchar255DatabaseTableColumn::create('role'),
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
            NotNullVarchar255DatabaseTableColumn::create('game'),
            IntDatabaseTableColumn::create('avatarFileID')
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

    DatabaseTable::create('rp1_point_account')
        ->columns([
            ObjectIdDatabaseTableColumn::create('accountID'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            NotNullVarchar255DatabaseTableColumn::create('description')
                ->defaultValue(''),
            NotNullVarchar255DatabaseTableColumn::create('game'),
        ])
        ->indices([
            DatabaseTablePrimaryIndex::create()
                ->columns(['accountID']),
        ]),

    PartialDatabaseTable::create('rp1_item_to_raid')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['pointAccountID'])
                ->referencedTable('rp1_point_account')
                ->referencedColumns(['accountID'])
                ->onDelete('SET NULL'),
        ]),

    DatabaseTable::create('rp1_raid')
        ->columns([
            ObjectIdDatabaseTableColumn::create('raidID'),
            NotNullVarchar255DatabaseTableColumn::create('game'),
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
            VarcharDatabaseTableColumn::create('classification')
                ->length(255),
            NotNullVarchar255DatabaseTableColumn::create('role'),
        ])
        ->indices([
            DatabaseTableIndex::create('raidID_characterID')
                ->columns(['raidID', 'characterID'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
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
        ]),

    DatabaseTable::create('rp1_raid_event')
        ->columns([
            ObjectIdDatabaseTableColumn::create('eventID'),
            NotNullVarchar255DatabaseTableColumn::create('title')
                ->defaultValue(''),
            IntDatabaseTableColumn::create('pointAccountID')
                ->length(10),
            NotNullVarchar255DatabaseTableColumn::create('game'),
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
        ]),

    PartialDatabaseTable::create('rp1_raid')
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['raidEventID'])
                ->referencedTable('rp1_raid_event')
                ->referencedColumns(['eventID'])
                ->onDelete('CASCADE'),
        ]),

    DatabaseTable::create('rp1_server')
        ->columns([
            ObjectIdDatabaseTableColumn::create('serverID'),
            NotNullVarchar255DatabaseTableColumn::create('game'),
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
            DatabaseTableIndex::create('identifier_game')
                ->columns(['identifier', 'game'])
                ->type(DatabaseTableIndex::UNIQUE_TYPE),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['packageID'])
                ->referencedTable('wcf1_package')
                ->referencedColumns(['packageID'])
                ->onDelete('CASCADE'),
        ]),
];
