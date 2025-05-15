<?php

namespace rp\form;

use wcf\system\request\LinkHandler;
use wcf\util\HeaderUtil;

/**
 * Shows the character search form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterSearchForm extends \rp\acp\form\CharacterSearchForm
{
    /**
     * @inheritDoc
     */
    public $neededPermissions = [];

    public function saved(): void
    {
        // forward to result page
        HeaderUtil::redirect(LinkHandler::getInstance()->getLink(
            'CharacterList',
            [
                'application' => 'rp',
                'id' => $this->searchObj->searchID,
            ]
        ));
        exit;
    }
}
