<?php

namespace rp\system\gridView\admin;

use rp\acp\form\CharacterEditForm;
use rp\data\character\CharacterProfile;
use rp\data\character\CharacterProfileList;
use rp\event\gridView\admin\CharacterGridViewInitialized;
use rp\system\game\GameHandler;
use rp\system\interaction\admin\CharacterInteractions;
use rp\system\interaction\bulk\admin\CharacterBulkInteractions;
use wcf\acp\form\UserEditForm;
use wcf\data\DatabaseObject;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\BooleanFilter;
use wcf\system\gridView\filter\SelectFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\filter\TimeFilter;
use wcf\system\gridView\filter\UserFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\AbstractColumnRenderer;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\TimeColumnRenderer;
use wcf\system\gridView\renderer\UserLinkColumnRenderer;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\interaction\ToggleInteraction;
use wcf\system\WCF;

/**
 * Grid view for the character list page.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterGridView extends AbstractGridView
{

    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for("characterID")
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer()),
            GridViewColumn::for("avatar")
                ->label('rp.character.avatar')
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function getClasses(): string
                        {
                            return 'gridView__column--icon';
                        }

                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof CharacterProfile);

                            return $row->getAvatar()->getImageTag(24);
                        }
                    }
                ),
            GridViewColumn::for("characterName")
                ->label('rp.character.characterName')
                ->titleColumn()
                ->sortable()
                ->filter(new TextFilter())
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
                ->label('rp.acp.character.owner')
                ->renderer(new UserLinkColumnRenderer(UserEditForm::class))
                ->filter(new UserFilter()),
            GridViewColumn::for('gameID')
                ->label('rp.acp.character.game')
                ->filter(new SelectFilter(GameHandler::getInstance()->getGames())
                ->hidden(),
            GridViewColumn::for('created')
                ->label('rp.character.created')
                ->sortable()
                ->renderer(new TimeColumnRenderer())
                ->filter(new TimeFilter()),
            GridViewColumn::for('isPrimary')
                ->label('rp.character.primary')
                ->filter(new BooleanFilter())
                ->hidden(),
        ]);

        $provider = new CharacterInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(CharacterEditForm::class, static function (CharacterProfile $character): bool {
                return $character->canEdit();
            }),
        ]);
        $this->setInteractionProvider($provider);
        $this->setBulkInteractionProvider(new CharacterBulkInteractions());

        $this->addQuickInteraction(
            new ToggleInteraction(
                'enable',
                'rp/characters/%s/enable',
                'rp/characters/%s/disable',
            )
        );

        $this->setSortField('characterName');
        $this->addRowLink(new GridViewRowLink(CharacterEditForm::class));
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
