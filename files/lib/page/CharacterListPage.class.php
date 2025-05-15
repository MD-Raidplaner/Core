<?php

namespace rp\page;

use rp\data\character\CharacterProfileList;
use wcf\data\search\Search;
use wcf\page\SortablePage;
use wcf\system\exception\IllegalLinkException;
use wcf\system\request\LinkHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows character list page.
 *
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 * 
 * @property    CharacterProfileList    $objectList
 */
class CharacterListPage extends SortablePage
{
    /**
     * available letters
     */
    public static string $availableLetters = '#ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * @inheritDoc
     */
    public $defaultSortField = RP_CHARACTERS_LIST_DEFAULT_SORT_FIELD;

    /**
     * @inheritDoc
     */
    public $defaultSortOrder = RP_CHARACTERS_LIST_DEFAULT_SORT_ORDER;

    /**
     * @inheritDoc
     */
    public $itemsPerPage = RP_CHARACTERS_LIST_PER_PAGE;

    /**
     * letter
     */
    public string $letter = '';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.rp.canViewCharacterList'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = CharacterProfileList::class;

    /**
     * own characters
     */
    public int $ownCharacters = 0;

    /**
     * character search
     */
    public ?Search $search = null;

    /**
     * id of a character search
     */
    public int $searchID = 0;

    /**
     * @inheritDoc
     */
    public $validSortFields = ['characterName', 'created'];

    /**
     * @inheritDoc
     */
    public function assignVariables(): void
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'letters' => \str_split(self::$availableLetters),
            'letter' => $this->letter,
            'ownCharacters' => $this->ownCharacters,
            'searchID' => $this->searchID,
            'validSortFields' => $this->validSortFields,
        ]);

        if (\count($this->objectList) === 0) {
            @\header('HTTP/1.1 404 Not Found');
        }
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList(): void
    {
        parent::initObjectList();

        if ($this->search !== null) {
            $searchData = \unserialize($this->search->searchData);
            $this->objectList->getConditionBuilder()->add("characterID IN (?)", [$searchData['matches']]);
            unset($searchData);
        }

        if (!empty($this->letter)) {
            if ($this->letter == '#') {
                $this->objectList->getConditionBuilder()->add("SUBSTRING(characterName,1,1) IN ('0', '1', '2', '3', '4', '5', '6', '7', '8', '9')");
            } else {
                $this->objectList->getConditionBuilder()->add("characterName LIKE ?", [$this->letter . '%']);
            }
        }

        if ($this->ownCharacters) {
            $this->objectList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
        }

        $this->objectList->getConditionBuilder()->add('isDisabled = ?', [0]);
    }

    /**
     * @inheritDoc
     */
    protected function readObjects(): void
    {
        parent::readObjects();

        $userIDs = \array_unique(
            \array_filter(
                \array_map(
                    fn ($character) => $character->userID,
                    $this->objectList->getObjects()
                ),
                fn ($userID) => $userID !== null
            )
        );

        if (!empty($userIDs)) {
            // preload user storage to avoid reading storage for each character separately at runtime
            UserStorageHandler::getInstance()->loadStorage($userIDs);
        }
    }

    /**
     * @inheritDoc
     */
    public function readParameters(): void
    {
        parent::readParameters();

        // letter
        if (
            isset($_REQUEST['letter'])
            && \mb_strlen($_REQUEST['letter']) == 1
            && \str_contains(self::$availableLetters, $_REQUEST['letter'])
        ) {
            $this->letter = $_REQUEST['letter'];
        }

        if (!empty($_REQUEST['id'])) {
            $this->searchID = \intval($_REQUEST['id']);
            $this->search = new Search($this->searchID);
            if (!$this->search->searchID || $this->search->userID != WCF::getUser()->userID || $this->search->searchType != 'characters') {
                throw new IllegalLinkException();
            }
        }

        if (!empty($_POST)) {
            $parameters = ['application' => 'rp'];
            if ($this->searchID) {
                $parameters['id'] = $this->searchID;
            }
            $url = \http_build_query($_POST, '', '&');
            HeaderUtil::redirect(LinkHandler::getInstance()->getLink('CharacterList', $parameters, $url));

            exit;
        }
    }
}
