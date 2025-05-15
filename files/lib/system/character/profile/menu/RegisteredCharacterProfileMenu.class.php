<?php

namespace rp\system\character\profile\menu;

use wcf\system\WCF;

/**
 * Represents a menu that is registered with the CharacterProfileMenuCollection event.
 * 
 * @author  Marco Daries
 * @copyright   2025 MD-Raidplaner
 * @license MD-Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class RegisteredCharacterProfileMenu
{
    private ICharacterProfileMenu $object;

    public function __construct(
        private readonly string $className
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getContent(int $characterID): string
    {
        return $this->getObject()->getContent($characterID);
    }

    public function getID(): string
    {
        return \str_replace('CharacterProfileMenu', '', $this->getIdentifier());
    }

    private function getIdentifier(): string
    {
        $parts = \explode('\\', $this->className);
        $className = \end($parts);
        $className = \lcfirst($className);
        return $className;
    }

    public function getName(): string
    {
        return WCF::getLanguage()->getDynamicVariable(\sprintf(
            'rp.character.profile.menu.%s',
            $this->getID(),
        ));
    }

    public function getObject(): ICharacterProfileMenu
    {
        return $this->object ??= new ($this->getClassName())();
    }

    public function isVisible(int $characterID): bool
    {
        return $this->getObject()->isVisible($characterID);
    }
}
