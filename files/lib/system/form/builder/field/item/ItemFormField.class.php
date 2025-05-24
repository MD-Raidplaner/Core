<?php

namespace rp\system\form\builder\field\item;

use rp\data\character\Character;
use rp\data\character\CharacterList;
use rp\data\point\account\PointAccountCache;
use rp\system\cache\runtime\CharacterRuntimeCache;
use wcf\data\IStorableObject;
use wcf\system\form\builder\data\processor\CustomFormDataProcessor;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\TDefaultIdFormField;
use wcf\system\form\builder\field\validation\FormFieldValidationError;
use wcf\system\form\builder\IFormDocument;
use wcf\system\WCF;

/**
 * Implementation of a form field for item.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
class ItemFormField extends AbstractFormField
{
    use TDefaultIdFormField;

    /**
     * @var Character[]
     */
    protected ?array $characters = null;
    protected $templateApplication = 'rp';
    protected $templateName = 'shared_itemFormField';
    protected $value = [];

    public function getCharacters(): array
    {
        if ($this->characters === null) {
            $characterList = new CharacterList();
            $characterList->getConditionBuilder()->add('isDisabled = ?', [0]);
            $characterList->sqlOrderBy = 'characterName ASC';
            $characterList->readObjects();
            $this->characters = $characterList->getObjects();
        }

        return $this->characters;
    }

    #[\Override]
    protected static function getDefaultId(): string
    {
        return 'items';
    }

    /**
     * Returns all point accounts.
     */
    public function getPointAccounts(): array
    {
        return PointAccountCache::getInstance()->getAccounts();
    }

    #[\Override]
    public function hasSaveValue(): bool
    {
        return false;
    }

    #[\Override]
    public function populate(): self
    {
        parent::populate();

        $this->getDocument()->getDataHandler()->addProcessor(new CustomFormDataProcessor(
            'items',
            function (IFormDocument $document, array $parameters) {
                if ($this->checkDependencies() && $this->getValue() !== null && !empty($this->getValue())) {
                    $parameters[$this->getObjectProperty()] = $this->getValue();
                }

                return $parameters;
            }
        ));

        return $this;
    }

    #[\Override]
    public function readValue(): void
    {
        if ($this->getDocument()->hasRequestData($this->getPrefixedId()) && \is_array($this->getDocument()->getRequestData($this->getPrefixedId()))) {
            $this->value = $this->getDocument()->getRequestData($this->getPrefixedId());
        } else {
            $this->value = [];
        }
    }

    #[\Override]
    public function validate(): void
    {
        if ($this->isRequired() && ($this->getValue() === null || empty($this->getValue()))) {
            $this->addValidationError(new FormFieldValidationError('empty'));
        } else {
            $items = [];
            foreach ($this->getValue() as $item) {
                if (!\is_array($item)) continue;
                foreach (['characterID', 'itemID', 'pointAccountID', 'points'] as $key) {
                    if (!\array_key_exists($key, $item)) continue 2;
                }
                $items[] = $item;
            }

            $this->value($items);
        }

        parent::validate();
    }

    #[\Override]
    public function value($value): self
    {
        if (!\is_array($value)) $value = [];
        return parent::value($value);
    }
}
