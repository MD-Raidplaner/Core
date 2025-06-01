<?php

namespace rp\system\gridView\admin;

use rp\acp\form\PointAccountEditForm;
use rp\data\point\account\I18nPointAccountList;
use rp\event\gridView\admin\PointAccountGridViewInitialized;
use rp\system\game\GameHandler;
use rp\system\gridView\renderer\GameColumnRenderer;
use rp\system\interaction\admin\PointAccountInteractions;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\SelectFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\WCF;

/**
 * Grid view for the point account list page.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class PointAccountGridView extends AbstractGridView
{
    public function __construct()
    {

        $this->addColumns([
            GridViewColumn::for('accountID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('title')
                ->label('wcf.global.title')
                ->titleColumn()
                ->filter(new I18nTextFilter())
                ->sortable(sortByDatabaseColumn: 'titleI18n'),
            GridViewColumn::for('game')
                ->label('rp.game.title')
                ->renderer(new GameColumnRenderer())
                ->filter(new SelectFilter(GameHandler::getInstance()->getGames())),
        ]);

        $provider = new PointAccountInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(PointAccountEditForm::class),
        ]);
        $this->setInteractionProvider($provider);

        $this->setSortField('title');
        $this->addRowLink(new GridViewRowLink(PointAccountEditForm::class));
    }

    #[\Override]
    protected function createObjectList(): I18nPointAccountList
    {
        return new I18nPointAccountList();
    }

    #[\Override]
    protected function getInitializedEvent(): PointAccountGridViewInitialized
    {
        return new PointAccountGridViewInitialized($this);
    }

    #[\Override]
    public function isAccessible(): bool
    {
        if (
            \RP_POINTS_ENABLED &&
            \RP_ITEM_ACCOUNT_EASYMODE_DISABLED &&
            WCF::getSession()->getPermission('admin.rp.canManagePointAccount')
        ) {
            return true;
        }

        return false;
    }
}
