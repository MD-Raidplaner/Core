<?php

namespace rp\system\listView\user;

use rp\data\character\CharacterProfileList;
use rp\event\listView\user\CharacterListViewInitialized;
use rp\system\interaction\user\CharacterInteractions;
use wcf\data\DatabaseObjectList;
use wcf\event\IPsr14Event;
use wcf\system\listView\AbstractListView;
use wcf\system\listView\filter\BooleanFilter;
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
final class CharacterListView extends AbstractListView
{
    public function __construct()
    {
        $this->addAvailableSortFields([
            new ListViewSortField('created', 'wcf.global.date'),
            new ListViewSortField('characterName', 'rp.character.characterName'),
        ]);

        $this->addAvailableFilters([
            new TextFilter('characterName', 'rp.character.characterName'),
            $this->getUserFilter(),
            $this->getOwnCharactersFilter(),
        ]);

        $this->setInteractionProvider(new CharacterInteractions());
        $this->setItemsPerPage(\RP_CHARACTERS_LIST_PER_PAGE);
        $this->setSortField(\RP_CHARACTERS_LIST_DEFAULT_SORT_FIELD);
        $this->setSortOrder(\RP_CHARACTERS_LIST_DEFAULT_SORT_ORDER);
    }

    #[\Override]
    protected function createObjectList(): CharacterProfileList
    {
        return new CharacterProfileList();
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new CharacterListViewInitialized($this);
    }

    protected function getOwnCharactersFilter(): BooleanFilter
    {
        return new class('ownCharacters', 'rp.character.ownCharacters') extends BooleanFilter {
            #[\Override]
            public function applyFilter(DatabaseObjectList $list, string $value): void
            {
                $list->getConditionBuilder()->add('member.userID = ?', [WCF::getUser()->userID]);
            }
        };
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
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('user.rp.canViewCharacterList');
    }

    #[\Override]
    public function renderItems(): string
    {
        return WCF::getTPL()->render('rp', 'characterListItems', ['view' => $this]);
    }
}
