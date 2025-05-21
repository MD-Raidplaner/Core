<?php

namespace rp\form;

use rp\data\character\CharacterList;
use rp\page\CharacterListPage;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\search\Search;
use wcf\data\search\SearchEditor;
use wcf\form\AbstractForm;
use wcf\form\AbstractFormBuilderForm;
use wcf\system\exception\UserInputException;
use wcf\system\form\builder\container\FormContainer;
use wcf\system\form\builder\container\TabFormContainer;
use wcf\system\form\builder\container\TabMenuFormContainer;
use wcf\system\form\builder\data\processor\VoidFormDataProcessor;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Shows the character search form.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class CharacterSearchForm extends AbstractFormBuilderForm
{
    /**
     * list with searched characters
     */
    public CharacterList $characterList;

    /**
     * list of grouped character group assignment condition object types
     * @var ObjectType[][]
     */
    public array $conditions = [];

    /**
     * results per page
     */
    public int $itemsPerPage = 50;

    /**
     * number of results
     */
    public int $maxResults = 2000;

    /**
     * search object
     */
    protected ?Search $searchObj = null;

    /**
     * sort field
     */
    public string $sortField = 'characterName';

    /**
     * sort order
     */
    public string $sortOrder = 'ASC';

    #[\Override]
    public function createForm(): void
    {
        $objectTypes = ObjectTypeCache::getInstance()->getObjectTypes('de.md-raidplaner.rp.condition.characterSearch');
        foreach ($objectTypes as $objectType) {
            if (!$objectType->conditiongroup) continue;
            $this->conditions[$objectType->conditiongroup] ??= [];
            $this->conditions[$objectType->conditiongroup][$objectType->objectTypeID] = $objectType;
        }

        parent::createForm();

        $tabMenu = TabMenuFormContainer::create('characterSearchTabMenu');
        $this->form->appendChild($tabMenu);

        foreach ($this->conditions as $conditionGroup => $conditionObjectTypes) {
            $tab = TabFormContainer::create('character_' . $conditionGroup . 'Tab');
            $tab->label('rp.character.condition.conditionGroup.' . $conditionGroup);
            $tabMenu->appendChild($tab);

            $container = FormContainer::create('character_' . $conditionGroup);
            $tab->appendChild($container);

            foreach ($conditionObjectTypes as $condition) {
                $container->appendChild($condition->getProcessor()->getFormField());
                $this->form->getDataHandler()->addProcessor(new VoidFormDataProcessor($condition->getProcessor()->getID()));
            }
        }
    }

    #[\Override]
    public function save(): void
    {
        AbstractForm::save();

        // store search result in database
        $data = \serialize([
            'itemsPerPage' => $this->itemsPerPage,
            'matches' => $this->characterList->getObjectIDs(),
        ]);

        $this->searchObj = SearchEditor::create([
            'searchData' => $data,
            'searchTime' => TIME_NOW,
            'searchType' => 'characters',
            'userID' => WCF::getUser()->userID ?? null,
        ]);

        $this->saved();

        // forward to result page
        HeaderUtil::redirect(LinkHandler::getInstance()->getControllerLink(
            CharacterListPage::class,
            [
                'id' => $this->searchObj->searchID,
            ]
        ));
        exit;
    }

    /**
     * Search for characters which fit to the search values.
     */
    private function search(): void
    {
        $this->characterList = new CharacterList();
        $this->characterList->sqlLimit = $this->maxResults;

        // read character ids
        foreach ($this->conditions as $groupedObjectType) {
            foreach ($groupedObjectType as $objectType) {
                $processor = $objectType->getProcessor();
                $processor->addObjectListCondition($this->characterList, $this->form);
            }
        }

        $this->characterList->readObjectIDs();
    }

    /**
     * @inheritDoc
     */
    public function validate(): void
    {
        parent::validate();

        $this->search();

        if (!\count($this->characterList->getObjectIDs())) {
            throw new UserInputException('search', 'noMatches');
        }
    }
}
