<?php

namespace rp\acp\page;

use CuyZ\Valinor\Mapper\MappingError;
use rp\system\gridView\admin\CharacterGridView;
use wcf\http\Helper;
use wcf\page\AbstractGridViewPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows the result of a character search.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 *
 * @extends AbstractGridViewPage<CharacterGridView>
 */
final class CharacterListPage extends AbstractGridViewPage
{
    public $neededPermissions = ['admin.rp.canSearchCharacter'];

    /**
     * id of a character search
     */
    public ?int $searchID;

    #[\Override]
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'searchID' => $this->searchID,
        ]);
    }

    #[\Override]
    protected function createGridView(): CharacterGridView
    {
        return new CharacterGridView($this->searchID ?? null);
    }

    #[\Override]
    protected function initGridView(): void
    {
        parent::initGridView();

        if ($this->searchID) {
            $this->gridView->setBaseUrl(LinkHandler::getInstance()->getControllerLink(static::class, [
                'id' => $this->searchID,
            ]));
        }
    }

    #[\Override]
    public function readParameters(): void
    {
        parent::readParameters();

        try {
            $parameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        id?: positive-int
                    }
                    EOT
            );

            $this->searchID = $parameters['id'] ?? null;
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }

    #[\Override]
    public function show(): void
    {
        $this->activeMenuItem = 'rp.acp.menu.link.character.' . (isset($this->searchID) ? 'search' : 'list');

        parent::show();
    }
}
