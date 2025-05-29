<?php

namespace rp\system\listView\user;

use rp\data\character\CharacterProfileList;
use rp\event\listView\user\PointListViewInitialized;
use wcf\data\DatabaseObjectList;
use wcf\system\listView\AbstractListView;
use wcf\system\listView\filter\TextFilter;
use wcf\system\listView\ListViewSortField;
use wcf\system\WCF;

/**
 * List view for the list of characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @extends AbstractListView<CharacterProfileList>
 */
final class PointListView extends AbstractListView
{
    public function __construct()
    {
        $this->addAvailableSortFields([
            new ListViewSortField('characterName', 'rp.character.characterName'),
        ]);
        $this->addAvailableFilters([
            new TextFilter('characterName', 'rp.character.characterName'),
            $this->getUserFilter(),
        ]);

        $this->setSortField('characterName');
        $this->setItemsPerPage(30);
    }

    #[\Override]
    protected function createObjectList(): CharacterProfileList
    {
        $characterList = new CharacterProfileList();

        $characterList->getConditionBuilder()->add('member.isDisabled = ?', [0]);
        if (!\RP_SHOW_TWINKS) $characterList->getConditionBuilder()->add('member.isPrimary = ?', [1]);

        return $characterList;
    }

    #[\Override]
    protected function getInitializedEvent(): ?PointListViewInitialized
    {
        return new PointListViewInitialized($this);
    }

    protected function getUserFilter(): TextFilter
    {
        return new class('username', 'wcf.user.username') extends TextFilter {
            #[\Override]
            public function applyFilter(DatabaseObjectList $list, string $value): void
            {
                $list->getConditionBuilder()->add(
                    "member.userID IN ( 
                        SELECT  userID
                        FROM    wcf1_user
                        WHERE   username LIKE ?
                    )",
                    [\sprintf('%%%s%%', WCF::getDB()->escapeLikeValue($value))]
                );
            }
        };
    }

    #[\Override]
    public function renderItems(): string
    {
        return WCF::getTPL()->render('rp', 'pointListItems', ['view' => $this]);
    }
}
