<?php

namespace rp\system\gridView\admin;

use rp\acp\form\RaidEventEditForm;
use rp\data\raid\event\I18nRaidEventList;
use rp\data\raid\event\RaidEvent;
use rp\event\gridView\admin\RaidEventGridViewInitialized;
use rp\system\interaction\admin\RaidEventInteractions;
use wcf\data\DatabaseObject;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\PhraseColumnRenderer;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the raid event list page.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RaidEventGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('eventID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('title')
                ->label('wcf.global.title')
                ->titleColumn()
                ->renderer(new PhraseColumnRenderer())
                ->filter(new I18nTextFilter())
                ->sortable(sortByDatabaseColumn: 'titleI18n'),
            GridViewColumn::for('pointAccountID')
                ->label('rp.point.account.title')
                ->renderer(
                    new class extends DefaultColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof RaidEvent);

                            return StringUtil::encodeHTML($row->getPointAccount()?->getTitle() ?? 'unknown');
                        }
                    }
                )
                ->sortable(),
            GridViewColumn::for('defaultPoints')
                ->label('rp.acp.raid.event.defaultPoints')
                ->filter(new NumericFilter())
                ->sortable(),
        ]);

        $provider = new RaidEventInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(RaidEventEditForm::class),
        ]);
        $this->setInteractionProvider($provider);


        $this->setSortField('title');
        $this->setRowsPerPage(50);
        $this->addRowLink(new GridViewRowLink(RaidEventEditForm::class));
    }

    #[\Override]
    protected function createObjectList(): I18nRaidEventList
    {
        return new I18nRaidEventList();
    }

    #[\Override]
    protected function getInitializedEvent(): RaidEventGridViewInitialized
    {
        return new RaidEventGridViewInitialized($this);
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.rp.canManageRaidEvent');
    }
}
