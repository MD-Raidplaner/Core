<?php

namespace rp\page;

use CuyZ\Valinor\Mapper\MappingError;
use rp\data\character\CharacterProfileList;
use rp\data\point\account\PointAccountCache;
use wcf\http\Helper;
use wcf\page\MultipleLinkPage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\SystemException;
use wcf\system\WCF;

/**
 * Shows the point list page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class PointListPage extends MultipleLinkPage
{
    public static string $availableLetters = '#ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public $itemsPerPage = 60;
    public string $letter = '';
    public $objectListClassName = CharacterProfileList::class;
    public $sortField = 'characterName';
    public $sortOrder = 'ASC';

    #[\Override]
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'letter' => $this->letter,
            'letters' => \str_split(self::$availableLetters),
            'pointAccounts' => PointAccountCache::getInstance()->getAccounts()
        ]);
    }

    #[\Override]
    protected function initObjectList(): void
    {
        parent::initObjectList();

        $this->objectList->getConditionBuilder()->add('isDisabled = ?', [0]);
        if (!RP_SHOW_TWINKS) $this->objectList->getConditionBuilder()->add('isPrimary = ?', [1]);

        if (!empty($this->letter)) {
            if ($this->letter == '#') {
                $this->objectList->getConditionBuilder()->add("SUBSTRING(characterName,1,1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9')");
            } else {
                $this->objectList->getConditionBuilder()->add("characterName LIKE ?", [$this->letter . '%']);
            }
        }
    }

    #[\Override]
    public function readParameters(): void
    {
        parent::readParameters();

        // letter
        if (isset($_REQUEST['letter']) && \mb_strlen($_REQUEST['letter']) == 1 && \mb_strpos(self::$availableLetters, $_REQUEST['letter']) !== false) {
            $this->letter = $_REQUEST['letter'];
        }

        try {
            $parameters = Helper::mapQueryParameters(
                $_GET,
                <<<'EOT'
                    array {
                        letter?: string
                    }
                    EOT
            );

            if (isset($parameters['letter'])) {
                $value = $parameters['letter'];
                // Check that the value is exactly one character and is contained in the available letters
                if (\mb_strlen($value) !== 1 || \mb_strpos(self::$availableLetters, $value) === false) {
                    throw new SystemException("The value for 'letter' must be a single valid character.");
                }

                $this->letter = $value;
            }
        } catch (MappingError) {
            throw new IllegalLinkException();
        }
    }
}
