<?php

namespace rp\system\gridView\admin;

use rp\data\character\CharacterProfile;
use rp\data\character\CharacterProfileList;
use rp\event\gridView\admin\CharacterGridViewInitialized;
use wcf\acp\form\UserEditForm;
use wcf\data\DatabaseObject;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\TimeColumnRenderer;
use wcf\system\gridView\renderer\UserLinkColumnRenderer;
use wcf\system\view\filter\BooleanFilter;
use wcf\system\view\filter\TextFilter;
use wcf\system\view\filter\TimeFilter;
use wcf\system\view\filter\UserFilter;
use wcf\system\WCF;

/**
 * Grid view for a list of characters.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @extends AbstractGridView<CharacterProfile, CharacterProfileList>
 */
final class CharacterGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for("characterID")
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer()),
            GridViewColumn::for("characterName")
                ->label('rp.character.characterName')
                ->titleColumn()
                ->sortable()
                ->filter(TextFilter::class)
                ->renderer(
                    new class extends DefaultColumnRenderer {
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof CharacterProfile);

                            if (!$row->isPrimary) {
                                $primary = \sprintf(
                                    ' (%s: %s)',
                                    WCF::getLanguage()->get('rp.character.primary'),
                                    $row->getPrimaryCharacter()->getTitle()
                                );
                            }

                            $title = \sprintf(
                                '<p>%s%s</p>',
                                $row->getTitle(),
                                $primary ?? ''
                            );

                            return $title;
                        }
                    }
                ),
            GridViewColumn::for("userID")
                ->label('rp.character.owner')
                ->renderer(new UserLinkColumnRenderer(UserEditForm::class))
                ->sortable(sortByDatabaseColumn: 'username')
                ->filter(UserFilter::class),
            GridViewColumn::for('created')
                ->label('rp.character.created')
                ->sortable()
                ->renderer(new TimeColumnRenderer())
                ->filter(TimeFilter::class),
            GridViewColumn::for('isPrimary')
                ->label('rp.character.primary')
                ->filter(BooleanFilter::class)
                ->hidden(),
        ]);

        $this->setDefaultSortField('characterName');
    }

    #[\Override]
    protected function createObjectList(): CharacterProfileList
    {
        return new CharacterProfileList();
    }

    #[\Override]
    protected function getInitializedEvent(): CharacterGridViewInitialized
    {
        return new CharacterGridViewInitialized($this);
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.rp.canEditCharacter');
    }
}
